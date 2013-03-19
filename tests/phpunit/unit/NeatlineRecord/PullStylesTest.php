<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Tests for `pullStyles` on `NeatlineRecord`.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineRecordTest_PullStyles extends Neatline_TestCase
{


    /**
     * `pullStyles` should update record values to match exhibit CSS.
     */
    public function testPullStyles()
    {

        $exhibit = $this->__exhibit();
        $exhibit->styles = "
            .tag1 {
              fill-color: 1;
              fill-opacity: 2;
            }
            .tag2 {
              stroke-color: 3;
              stroke-opacity: 4;
            }
            .tag3 {
              select-color: 5;
              select-opacity: 6;
            }
        ";
        $exhibit->save();
        $record = new NeatlineRecord($exhibit);

        // Pull `tag1` and `tag2`, save.
        $record->pullStyles(array('tag1', 'tag2'));
        $record->save();
        $record = $this->reload($record);

        // Should pull `tag1` and `tag2`.
        $this->assertEquals($record->fill_color, '1');
        $this->assertEquals($record->fill_opacity, 2);
        $this->assertEquals($record->stroke_color, '3');
        $this->assertEquals($record->stroke_opacity, 4);

        // Should not pull `tag3`.
        $this->assertNull($record->select_color);
        $this->assertNull($record->select_opacity);

    }


    /**
     * Rules with `auto` values should be ignored.
     */
    public function testIgnoreAutoStyles()
    {

        $exhibit = $this->__exhibit();
        $exhibit->styles = "
            .tag {
              fill-color: auto;
            }
        ";
        $exhibit->save();
        $record = new NeatlineRecord($exhibit);

        // Pull `tag1`, save.
        $record->pullStyles(array('tag'));
        $record->save();
        $record = $this->reload($record);

        // `auto` value should be ignored.
        $this->assertNull($record->fill_color);

    }


    /**
     * Rules with invalid properties should be ignored.
     */
    public function testIgnoreInvalidProperties()
    {

        $exhibit = $this->__exhibit();
        $exhibit->styles = "
            .tag {
              fill-color: color;
              invalid: value;
            }
        ";
        $exhibit->save();
        $record = new NeatlineRecord($exhibit);

        // Pull `tag1`, save.
        $record->pullStyles(array('tag'));
        $record->save();
        $record = $this->reload($record);

        // Invalid property should be ignored.
        $this->assertEquals($record->fill_color, 'color');

    }


}