/// <reference path="../includes/wsuwp-shortcode-fundselector-utils.js" />

jQuery(document).ready(function($) {
    QUnit.module( "unit tests", {}, function() {

        QUnit.module( "Gift amount rounding", {}, function() {
            QUnit.test( "Round gift amount down", function( assert ) {
                var amount = 6.313;
                assert.equal( 6.31, wsuwpUtils.roundAmount(amount), "Gift amount rounded down" );
            });

            QUnit.test( "Round gift amount up", function( assert ) {
                var amount = 6.319;
                assert.equal( 6.32, wsuwpUtils.roundAmount(amount), "Gift amount rounded up" );

                amount = 6.315;
                assert.equal( 6.32, wsuwpUtils.roundAmount(amount), "Gift amount rounds up when 5");
            });
        });

        QUnit.module( "Gift amount valid", {}, function() {
            QUnit.test( "Rejects non-numbers", function( assert ) {
                var amount = "a";
                var minAmount = 2;
                var maxAmount = 10;

                assert.notOk( wsuwpUtils.validateAmount(amount, minAmount, maxAmount), "False when amount is a string" );

                amount = [];
                assert.notOk( wsuwpUtils.validateAmount(amount, minAmount, maxAmount), "False when amount is an array" );
            });

            QUnit.test( "Checks minimum and maximum amount", function( assert ) {
                var amount = 1.2;
                var minAmount = 2.3;
                var maxAmount = 10.0;

                assert.notOk( wsuwpUtils.validateAmount(amount, minAmount, maxAmount), "False when amount less than minimum" );

                amount = 12.3;
                assert.notOk( wsuwpUtils.validateAmount(amount, minAmount, maxAmount), "False when amount greater than maximum" );

                amount = 8.6;
                assert.ok( wsuwpUtils.validateAmount(amount, minAmount, maxAmount), "True when amount between minimum and maximum" );
            });
        });

        QUnit.module( "Html encoding and decoding", {}, function() {
            QUnit.test("Html decoding", function( assert ) {
                var encodedString = "&amp;&amp;&quot;Test&quot; &lt;abcdef&gt; &#x2F;&#39;";
                var expected = '&&"Test" <abcdef> \/\'';
                var result = wsuwpUtils.htmlDecode(encodedString);

                assert.equal(result, expected, "string gets decoded");
            });

            QUnit.test("Html encoding", function( assert ) {
                var decodedString = '&&"Test" <abcdef> \/\'';
                var expected = "&amp;&amp;&quot;Test&quot; &lt;abcdef&gt; &#x2F;&#39;";
                var result = wsuwpUtils.htmlEncode(decodedString);

                assert.equal(result, expected, "string gets encoded");
            });
        });

        QUnit.module( "Donation total", {}, function() {
            QUnit.test("Calculate donation total", function( assert ) {
                var donationList = [];
                var total = wsuwpUtils.getDonationTotal(donationList);

                assert.equal(0, total, 'Empty donation list returns 0 as sum');

                donationList = [{'amount': 1.5}, {'amount': 2}, {'amount': 5.99}];
                total = wsuwpUtils.getDonationTotal(donationList);
                assert.equal(total, 1.5 + 2 + 5.99, 'Correctly calculates donation total');
            });
        });

        QUnit.module( "Advancement fee", {}, function() {
            QUnit.test("Calculate advancement fee", function( assert ) {
                var donationList = [];
                var total = wsuwpUtils.getAdvFeeTotal(donationList);

                assert.equal(0, total, 'Empty donation list returns 0 as sum');

                donationList = [{'amount': 50}, {'amount': 25}, {'amount': 25}];
                wpData.adv_fee_percentage = 5;
                total = wsuwpUtils.getAdvFeeTotal(donationList);
                assert.equal(total, 5, 'Correctly calculates advancment fee when fee is an integer');

                wpData.adv_fee_percentage = 4.5;
                total = wsuwpUtils.getAdvFeeTotal(donationList);
                assert.equal(total, 4.5, 'Correctly calculates advancment fee when fee is a floating point number');
            });
        });
    })
});