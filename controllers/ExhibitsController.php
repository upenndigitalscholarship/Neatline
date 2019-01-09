<?php

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Neatline_ExhibitsController extends Neatline_Controller_Rest
{


    /**
     * Set the default model, get tables.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NeatlineExhibit');
        $this->_exhibits = $this->_helper->db->getTable('NeatlineExhibit');
    }


    // REST API:
    // ------------------------------------------------------------------------


    /**
     * Fetch exhibit via GET.
     * @REST
     */
    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        echo Zend_Json::encode($this->_helper->db->findById()->toArray());
    }


    /**
     * Update exhibit via PUT.
     * @REST
     */
    public function putAction()
    {

        $this->_helper->viewRenderer->setNoRender(true);

        // Update the exhibit.
        $exhibit = $this->_helper->db->findById();
        $exhibit->saveForm(Zend_Json::decode(file_get_contents(
            Zend_Registry::get('fileIn')), true
        ));

        // Propagate CSS.
        $exhibit->pushStyles();

        // Respond with exhibit data.
        echo Zend_Json::encode($exhibit->toArray());

    }


    // Admin CRUD actions:
    // ------------------------------------------------------------------------


    /**
     * Browse exhibits.
     */
    public function browseAction()
    {

        parent::browseAction();

        // By default, sort by added date.
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }

    }


    /**
     * Create a new exhibit.
     */
    public function addAction()
    {

        $exhibit = new NeatlineExhibit;
        $form = $this->_getExhibitForm($exhibit);

        // Process form submission.
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $exhibit->saveForm($form->getValues());
            $this->_helper->redirector('browse');
        }

        // Push form to view.
        $this->view->form = $form;

        // Queue JS/CSS.
        nl_queueAddForm();

    }


    /**
     * Edit exhibit settings.
     */
    public function editAction()
    {

        $exhibit = $this->_helper->db->findById();
        $form = $this->_getExhibitForm($exhibit);

        // Process form submission.
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $exhibit->saveForm($form->getValues());
            $this->_helper->redirector('browse');
        }

        // Push exhibit and form to view.
        $this->view->neatline_exhibit = $exhibit;
        $this->view->form = $form;

        // Queue JS/CSS.
        nl_queueEditForm();

    }


    /**
     * Import items from Omeka.
     */
    public function importAction()
    {

        $exhibit = $this->_helper->db->findById();

        if ($this->_request->isPost()) {

            // Save the query.
            $post = $this->_request->getPost();
            $exhibit->item_query = serialize($post);
            $exhibit->save();

            // Import items.
            Zend_Registry::get('job_dispatcher')->sendLongRunning(
                'Neatline_Job_ImportItems', array(
                    'exhibit_id'    => $exhibit->id,
                    'query'         => $post
                )
            );

            // Flash success.
            $this->_helper->flashMessenger(
                $this->_getImportStartedMessage(), 'success'
            );

            // Redirect to browse.
            $this->_helper->redirector('browse');

        }

        // Populate query.
        $query = unserialize($exhibit->item_query);
        $_REQUEST = $query; $_GET = $query;

    }

    public function exportSQL($exid){
        $db = get_db();
        $recordsTable = $db->getTable('NeatlineRecord');
        $rName = $recordsTable->getTableName();
        $query = "select id, owner_id, item_id, exhibit_id, added, modified, is_coverage, is_wms,  slug, title, item_title, body, astext(coverage), tags, widgets, presenter,   fill_color, fill_color_select, stroke_color, stroke_color_select, fill_opacity,    fill_opacity_select, stroke_opacity, stroke_opacity_select, stroke_width,    point_radius, zindex, weight, start_date, end_date, after_date, before_date, point_image, wms_address, wms_layers, min_zoom, max_zoom, map_zoom, map_focus
            from $rName where exhibit_id = {$exid}";
        $result = $db->query($query);
        if(!$result){
            die('Could not fetch records');
        }
        $head = array("id", "owner_id", "item_id", "exhibit_id", "added", "modified", "is_coverage", "is_wms",  "slug", "title", "item_title", "body", "coverage", "tags", "widgets",
            "presenter", "fill_color", "fill_color_select", "stroke_color", "stroke_color_select", "fill_opacity",    "fill_opacity_select", "stroke_opacity", "stroke_opacity_select", "stroke_width",    "point_radius", "zindex", "weight", "start_date", "end_date", "after_date", "before_date", "point_image", "wms_address", "wms_layers", "min_zoom", "max_zoom", "map_zoom", "map_focus");
        $path = BASE_DIR."/files/neatline_test.csv";
        $fp = fopen($path, 'w');
        $geometry = [];
        if ($fp && $result) {
            fputcsv($fp, array_values($head));
            while($row = $result->fetch()){
                $geometry[] =  array_values($row);
                fputcsv($fp, array_values($row));
            }
            fclose($fp);
        }
        return $geometry;
    }

    public function wktconvertion($input){
        if($input[0] == 'G'){
            $point = substr($input, 19, -1);
        }else{
            $point = $input;
        }
        // echo $point;

        if(substr($point, 0, 3) == "POL"){
            $value_input = substr($point, 9, -2);
            $type = "POL";
            // echo $value_input;
        }else if(substr($point, 0, 3) == "POI"){
            $value_input = substr($point, 6, -1);
            $type = "POI";
            // echo $value_input;
        }else if(substr($point, 0, 3) == "LIN"){
            $value_input = substr($point, 11, -1);
            $type = "LIN";
            // echo $value_input;
        }
        // $values_pair = substr($point, 6, -1);
        $values_pair = explode(",", $value_input);
        foreach($values_pair as $pair){
            $values = explode(" ", $pair);
            $lat = $values[1];
            $lon = $values[0];
            $lat_new = (360 / M_PI) * atan(pow(M_E, (M_PI * $lat / 20037508.34))) - 90;
            $lon_new = 180 * $lon / 20037508.34;
            $ret[] = array($lon_new, $lat_new);
        }
        if($type == "POL"){
            return array($ret);
        }else if($type == "LIN"){
            return $ret;
        }else{
            return $ret[0];
        }
    }
    public function exportGeoJson($geometry){
        foreach($geometry as $point){
            if($point[12][0] == 'G'){
                $input = substr($point[12], 19, -1);
            }else{
                $input = $point[12];
            }
            if(substr($input, 0, 3) == "POL"){
                $type = "Polygon";
            }else if(substr($input, 0, 3) == "POI"){
                $type = "Point";
            }else if(substr($input, 0, 3) == "LIN"){
                $type = "LineString";
            }
            $value = $this->wktconvertion($point[12]);
            $features[] = array(
            'type' => 'Feature',
            'properties' => array("id" => $point[0], "owner_id" => $point[1], "item_id" => $point[2], "exhibit_id" => $point[3], "added" => $point[4], "modified" => $point[5], "is_coverage" => $point[6], "is_wms" => $point[7],  "slug" => $point[8], "title" => $point[9], "item_title" => $point[10], "body" => $point[11], "tags" => $point[13], "widgets" => $point[14],
            "presenter" => $point[15], "fill_color" => $point[16], "fill_color_select" => $point[17], "stroke_color" => $point[18], "stroke_color_select" => $point[19], "fill_opacity" => $point[20],    "fill_opacity_select" => $point[21], "stroke_opacity" => $point[22], "stroke_opacity_select" => $point[23], "stroke_width" => $point[24],    "point_radius" => $point[25], "zindex" => $point[26], "weight" => $point[27], "start_date" => $point[28], "end_date" => $point[29], "after_date" => $point[30], "before_date" => $point[31], "point_image" => $point[32], "wms_address" => $point[33], "wms_layers" => $point[34], "min_zoom" => $point[35], "max_zoom" => $point[36], "map_zoom" => $point[37], "map_focus" => $point[38]),
            'geometry' => array(
                 'type' => $type,
                 'coordinates' => $value
            )

        );
        }
        $new_data = array(
            'type' => 'FeatureCollection',
            'features' => $features,
        );
        $final_data = json_encode($new_data, JSON_PRETTY_PRINT);
        $path = BASE_DIR."/files/exporting.geojson";
        $fp = fopen($path, 'w');
        fwrite($fp, $final_data);
        fclose($fp);
        return $final_data;
    }
    public function exportAction(){
        $exhibit = $this->_helper->db->findById();
        $fileid = $exhibit->id;
        $filename = "export-exhibit-" . $exhibit->id . date('-Y-m-d-H-i-s');
        $geometry = $this->exportSQL($fileid);
        $geojson = $this->exportGeoJson($geometry);
        // echo $geojson;
        $file_path_csv = WEB_RELATIVE_FILES.'/neatline_test.csv';
        $file_csv = "<a href= {$file_path_csv} download={$filename}.csv>Download CSV</a>";
        $file_path_geojson = WEB_RELATIVE_FILES.'/exporting.geojson';
        $file_geojson = "<a href= {$file_path_geojson} download={$filename}.geojson>Download GeoJSON</a>";

        $this->view->file_csv = $file_csv;
        $this->view->file_geojson = $file_geojson;
    }

    /**
     * Edit exhibit.
     */
    public function editorAction()
    {

        // Assign exhibit to view.
        $exhibit = $this->_helper->db->findById();
        $this->view->neatline_exhibit = $exhibit;

        // Queue static assets.
        nl_queueNeatlineEditor($exhibit);

    }


    // Public views:
    // ------------------------------------------------------------------------


    /**
     * Show exhibit.
     */
    public function showAction()
    {

        $this->_helper->viewRenderer->setNoRender(true);

        // Try to find an exhibit with the requested slug.
        $exhibit = $this->_exhibits->findBySlug($this->_request->slug);
        if (!$exhibit) throw new Omeka_Controller_Exception_404;

        // Assign exhibit to view.
        $this->view->neatline_exhibit = $exhibit;

        // Queue static assets.
        nl_queueNeatlinePublic($exhibit);
        nl_queueExhibitTheme($exhibit);

        // Try to render exhibit-specific template.
        try { $this->render("themes/$exhibit->slug/show"); }
        catch (Exception $e) { $this->render('show'); }

    }


    /**
     * Show fullscreen exhibit.
     */
    public function fullscreenAction()
    {

        // Try to find an exhibit with the requested slug.
        $exhibit = $this->_exhibits->findBySlug($this->_request->slug);
        if (!$exhibit) throw new Omeka_Controller_Exception_404;

        // Assign exhibit to view.
        $this->view->neatline_exhibit = $exhibit;

        // Queue static assets.
        nl_queueNeatlinePublic($exhibit);
        nl_queueExhibitTheme($exhibit);
        nl_queueFullscreen($exhibit);

    }


    /**
     *
     */
    public function duplicateAction()
    {

        $exhibit = $this->_helper->db->findById();
        if (!$exhibit) throw new Omeka_Controller_Exception_404;

        $clone = clone $exhibit;
        $slugSuffix = 1;
        $newSlug = '';
        do {
          $newSlug = $clone->slug . '-' . $slugSuffix++;
        } while ($this->_exhibits->findBySlug($newSlug));
        $clone->slug = $newSlug;
        $clone->added = date('Y-m-d G:i:s');
        $clone->published = $clone->public ? date('Y-m-d G:i:s') : NULL;
        $titleSuffix = '';
        if (current_user()) {
          $clone->setOwner(current_user());
          $clone->owner_id = current_user()->id;
          $titleSuffix = __(' (%s copy)', current_user()->username);
        }
        else {
          $titleSuffix = __(' (Copy)');
        }
        $clone->title = $clone->title . $titleSuffix;
        $clone->save();

        Zend_Registry::get('job_dispatcher')->sendLongRunning(
            'Neatline_Job_DuplicateRecords', array(
                'exhibit_id'           => $exhibit->id,
                'adopting_exhibit_id'  => $clone->id,
                'owner_id'             => current_user() ? current_user()->id : NULL
            )
        );

        // Flash success.
        $this->_helper->flashMessenger(
            $this->_getDuplicateStartedMessage(), 'success'
        );

        // Redirect to browse.
        $this->_helper->redirector('browse');

    }


    // Helpers:
    // ------------------------------------------------------------------------


    /**
     * Return the pagination page length.
     *
     * Currently, $pluralName is ignored.
     */
    protected function _getBrowseRecordsPerPage($pluralName=null)
    {
        if (is_admin_theme()) return (int) get_option('per_page_admin');
        else return (int) get_option('per_page_public');
    }


    /**
     * Construct the details form.
     *
     * @param NeatlineExhibit $exhibit
     */
    protected function _getExhibitForm($exhibit)
    {
        return new Neatline_Form_Exhibit(array('exhibit' => $exhibit));
    }


    /**
     * Set the delete success message.
     *
     * @param NeatlineExhibit $exhibit
     */
    protected function _getDeleteSuccessMessage($exhibit)
    {
        return __('The exhibit "%s" was successfully deleted!',
            $exhibit->title
        );
    }


    /**
     * Set the delete confirm message.
     *
     * @param NeatlineExhibit $exhibit
     */
    protected function _getDeleteConfirmMessage($exhibit)
    {
        return __('This will delete "%s" and its associated metadata.',
            $exhibit->title
        );
    }


    /**
     * Set the import started message.
     */
    protected function _getImportStartedMessage()
    {
        return __('The item import was successfully started!');
    }


    /**
     * Set the duplication started message.
     */
    protected function _getDuplicateStartedMessage()
    {
        return __('The exhibit duplication was successfully started!');
    }


}
