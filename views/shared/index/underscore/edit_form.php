<?php

/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Edit form underscore template.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>


<!-- Record edit form. -->
<script id="edit-form" type="text/templates">

  <form class="form-stacked record">

    <p class="lead"></p>

    <ul class="nav nav-tabs">
      <li><a href="#form-text" data-toggle="tab">Text</a></li>
      <li><a href="#form-spatial" data-toggle="tab">Spatial</a></li>
      <li><a href="#form-style" data-toggle="tab">Style</a></li>
    </ul>

    <div class="tab-content">

      <div class="tab-pane" id="form-text">

        <div class="control-group">
          <label for="title"><?php echo __('Title'); ?></label>
          <div class="controls">
            <textarea name="title"></textarea>
          </div>
        </div>

        <div class="control-group">
          <label for="description"><?php echo __('Body'); ?></label>
          <div class="controls">
            <textarea name="body"></textarea>
          </div>
        </div>

      </div>

      <div class="tab-pane" id="form-spatial">

        <legend>Edit Geometry</legend>

        <div class="geometry">

          <label class="radio">
            <input type="radio" name="editMode" value="pan" checked>
            Navigate
          </label>

          <label class="radio">
            <input type="radio" name="editMode" value="point">
            Draw Point
          </label>

          <label class="radio">
            <input type="radio" name="editMode" value="line">
            Draw Line
          </label>

          <label class="radio">
            <input type="radio" name="editMode" value="poly">
            Draw Polygon
          </label>

          <label class="radio">
            <input type="radio" name="editMode" value="regPoly">
            Draw Regular Polygon
          </label>

          <div class="control-group indent regular-polygon">

            <div class="inline-inputs">
              <input type="text" name="sides" value="3" />
              Sides
            </div>

            <div class="inline-inputs">
              <input type="text" name="snap" value="5" />
              Snap Angle (degrees)
            </div>

            <label class="checkbox">
              <input type="checkbox" name="irregular">
              Irregular?
            </label>

          </div>

          <label class="radio">
            <input type="radio" name="editMode" value="modify">
            Modify Shape
          </label>

          <div class="control-group indent">

            <label class="checkbox">
              <input type="checkbox" name="modifyOptions" value="rotate">
              Rotate
            </label>

            <label class="checkbox">
              <input type="checkbox" name="modifyOptions" value="resize">
              Resize
            </label>

            <label class="checkbox">
              <input type="checkbox" name="modifyOptions" value="drag">
              Drag
            </label>

          </div>

          <label class="radio">
            <input type="radio" name="editMode" value="remove">
            Delete Shape
          </label>

        </div>

        <legend>Spatial Data</legend>

        <div class="control-group">
          <div class="controls">
            <textarea name="coverage"></textarea>
          </div>
        </div>

      </div>

      <div class="tab-pane" id="form-style">

        <label><?php echo __('Shape Color'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="vector-color" />
          </div>
        </div>

        <label><?php echo __('Line Color'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="stroke-color" />
          </div>
        </div>

        <label><?php echo __('Selected Color'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="select-color" />
          </div>
        </div>

        <label><?php echo __('Shape Opacity'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="vector-opacity" />
          </div>
        </div>

        <label><?php echo __('Selected Opacity'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="select-opacity" />
          </div>
        </div>

        <label><?php echo __('Line Opacity'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="stroke-opacity" />
          </div>
        </div>

        <label><?php echo __('Graphic Opacity'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="image-opacity" />
          </div>
        </div>

        <label><?php echo __('Line Width'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="stroke-width" />
          </div>
        </div>

        <label><?php echo __('Point Radius'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input type="text" name="point-radius" />
          </div>
        </div>

        <label><?php echo __('Point Graphic'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input name="point-image" type="text" />
          </div>
        </div>

        <label><?php echo __('Min Zoom'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input name="min-zoom" type="text" />
          </div>
        </div>

        <label><?php echo __('Max Zoom'); ?></label>
        <div class="controls">
          <div class="inline-inputs">
            <input name="max-zoom" type="text" />
          </div>
        </div>

      </div>

      <div class="tab-pane" id="form-tags">

      </div>

    </div>

    <?php echo $this->partial('index/underscore/_form_menu.php'); ?>

  </form>

</script>
