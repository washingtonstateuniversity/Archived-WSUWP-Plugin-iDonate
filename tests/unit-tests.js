/// <reference path="../includes/wsuwp-shortcode-fundselector-utils.js" />

jQuery(document).ready(function($) {
    QUnit.module( "unit tests", {}, function() {
        QUnit.test( "hello test", function( assert ) {
            assert.ok( 1 == "1", "Passed!" );
        });

        QUnit.test( "Round gift amount down", function( assert ) {
            var amount = 6.313;
            assert.ok( 6.31, wsuwpUtils.roundAmount(amount), "Gift amount rounded down" );
        });
    })
});