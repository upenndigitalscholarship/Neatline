
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Static bubble tests.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

describe('Static Bubble', function() {


  var el, layers, feature1, feature2;


  beforeEach(function() {

    _t.loadNeatline();
    _t.respondLast200(_t.json.StaticBubble.records);

    // Get layers and features.
    layers = _t.vw.MAP.getVectorLayers();
    feature1 = layers[0].features[0];
    feature2 = layers[1].features[0];

    // Set presenters.
    layers[0].nModel.set('presenter', 'StaticBubble');
    layers[1].nModel.set('presenter', 'StaticBubble');

    el = {
      title:  _t.vw.BUBBLE.$('.title'),
      body:   _t.vw.BUBBLE.$('.body'),
      close:  _t.vw.BUBBLE.$('.close')
    };

  });


  it('should show title on feature hover', function() {

    // --------------------------------------------------------------------
    // When the cursor hovers on a feature, the bubble should be populated
    // with values and the title should be displayed in the map container.
    // --------------------------------------------------------------------

    // Hover on feature.
    _t.hoverOnMapFeature(feature1);

    // Title and body should be rendered.
    expect(el.title.text()).toEqual('title1');
    expect(el.body.text()).toEqual('body1');

    // Bubble should be injected into map.
    expect(_t.vw.MAP.$el).toContain(_t.vw.BUBBLE.$el);

    // Title should be visible.
    expect(el.title).toBeVisible();

    // Body should be hidden.
    expect(el.body).not.toBeVisible();

  });


  it('should hide bubble on feature unhover', function() {

    // --------------------------------------------------------------------
    // When the cursor unhovers on a feature, the bubble should disappear.
    // --------------------------------------------------------------------

    // Highlight, unhighlight feature.
    _t.hoverOnMapFeature(feature1);
    _t.unHoverOnMapFeature();

    // Bubble should not be visible.
    expect(_t.vw.BUBBLE.$el).not.toBeVisible();

  });


  it('should hide bubble when the cursor leaves the exhibit', function() {

    // --------------------------------------------------------------------
    // When the cursor leaves the exhibit, the bubble should disappear.
    // --------------------------------------------------------------------

    // Move cursor out of the exhibit.
    _t.hoverOnMapFeature(feature1);
    _t.triggerMapMouseout();

    // Bubble should not be visible.
    expect(_t.vw.BUBBLE.$el).not.toBeVisible();

  });


  it('should freeze bubble on select', function() {

    // --------------------------------------------------------------------
    // When a feature is selected, the bubble should stay visible when the
    // cursor leaves the feature.
    // --------------------------------------------------------------------

    // Highlight feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Bubble should be visible on unhover.
    _t.unHoverOnMapFeature();
    expect(_t.vw.BUBBLE.$el).toBeVisible();

  });


  it('should show body and close "X" when body is not empty', function() {

    // --------------------------------------------------------------------
    // When a feature is selected and the record has a non-null body, the
    // body container and close "X" should be displayed.
    // --------------------------------------------------------------------

    // Set non-null body.
    layers[0].nModel.set('body', 'content');

    // Highlight feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Body, "X" should be visible.
    expect(el.close).toBeVisible();
    expect(el.body).toBeVisible();

  });


  it('should not show body and close "X" when body is empty', function() {

    // --------------------------------------------------------------------
    // When a feature is selected and the record has a null body, the body
    // container and close "X" should not be displayed.
    // --------------------------------------------------------------------

    // Set null body.
    layers[0].nModel.set('body', null);

    // Highlight feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Body, "X" should be not visible.
    expect(el.close).not.toBeVisible();
    expect(el.body).not.toBeVisible();

  });


  it('should add the `frozen` class on feature select', function() {

    // --------------------------------------------------------------------
    // When a feature is selected, the `frozen` class should be added.
    // --------------------------------------------------------------------

    // Highlight feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Should add `frozen` class.
    expect(_t.vw.BUBBLE.$el).toHaveClass('frozen');

  });


  it('should not respond to hover events when frozen', function() {

    // --------------------------------------------------------------------
    // When the bubble is frozen and the cursor hovers over a feature for
    // a different record, the bubble should not render the new record.
    // --------------------------------------------------------------------

    // Hover on feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Hover on a different feature.
    _t.hoverOnMapFeature(feature2);

    // Bubble values should be unchanged.
    expect(el.title.text()).toEqual('title1');
    expect(el.body.text()).toEqual('body1');

  });


  it('should respond to click events when frozen', function() {

    // --------------------------------------------------------------------
    // When the bubble is frozen and a different feature is clicked, the
    // bubble should render the new record.
    // --------------------------------------------------------------------

    // Hover on feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Hover on a different feature.
    _t.clickOnMapFeature(feature2);

    // Bubble values should change.
    expect(el.title.text()).toEqual('title2');
    expect(el.body.text()).toEqual('body2');

  });


  describe('close', function() {

    // --------------------------------------------------------------------
    // When a bubble is unfrozen by clicking on the map or the close "X",
    // the bubble should disappear and start responding to hover events.
    // --------------------------------------------------------------------

    beforeEach(function() {

      // Hover on feature, then select.
      _t.hoverOnMapFeature(feature1);
      _t.clickOnMapFeature(feature1);

    });

    afterEach(function() {

      // Bubble should disappear.
      expect(_t.vw.BUBBLE.$el).not.toBeVisible();

      // Hover on a different feature.
      _t.hoverOnMapFeature(feature2);

      // Bubble values should be changed.
      expect(el.title.text()).toEqual('title2');
      expect(el.body.text()).toEqual('body2');

    });

    it('should unfreeze bubble on close click', function() {
      el.close.trigger('click');
    });

    it('should unfreeze bubble on feature unselect', function() {
      _t.clickOffMapFeature();
    });

  });


  it('should hide body on unselect', function() {

    // --------------------------------------------------------------------
    // When a feature is unselected, the body should be hidden so that it
    // is not visible the next time the cursor hovers on a feature.
    // --------------------------------------------------------------------

    // Hover on feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Unselect the feature.
    _t.clickOffMapFeature();

    // Hover on feature again.
    _t.hoverOnMapFeature(feature1);

    // Body should be hidden.
    expect(el.body).not.toBeVisible();

  });


  it('should remove the `frozen` class on feature unselect', function() {

    // --------------------------------------------------------------------
    // When a feature is selected, the `frozen` class should be removed.
    // --------------------------------------------------------------------

    // Hover on feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Unselect the feature.
    _t.clickOffMapFeature();

    // Should remove `frozen` class.
    expect(_t.vw.BUBBLE.$el).not.toHaveClass('frozen');

  });


  it('should hide the bubble on deactivate', function() {

    // --------------------------------------------------------------------
    // When presenter is deactivated, the bubble should disappear.
    // --------------------------------------------------------------------

    // Hover on feature, then select.
    _t.hoverOnMapFeature(feature1);
    _t.clickOnMapFeature(feature1);

    // Deactivate the presenter.
    Neatline.vent.trigger('PRESENTER:deactivate');

    // Bubble should disappear.
    expect(_t.vw.BUBBLE.$el).not.toBeVisible();

  });


  it('should not respond to cursor events when deactivated', function() {

    // --------------------------------------------------------------------
    // The bubble should not respond to cursor events when deactivated.
    // --------------------------------------------------------------------

    // Deactivate the presenter.
    Neatline.vent.trigger('PRESENTER:deactivate');

    // Hover on feature.
    _t.hoverOnMapFeature(feature1);

    // Bubble should not be visible.
    expect(_t.vw.BUBBLE.$el).not.toBeVisible();

  });


  it('should respond to cursor events when activated', function() {

    // --------------------------------------------------------------------
    // When the presenter is activated after being deactivated, the bubble
    // should start responding to cursor events.
    // --------------------------------------------------------------------

    // Deactivate, activate the presenter.
    Neatline.vent.trigger('PRESENTER:deactivate');
    Neatline.vent.trigger('PRESENTER:activate');

    // Hover on feature.
    _t.hoverOnMapFeature(feature1);

    // Bubble should be visible.
    expect(_t.vw.BUBBLE.$el).toBeVisible();

  });


});
