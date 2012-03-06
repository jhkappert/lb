/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){var b=a("#mediaplayer form");a('input[type="submit"]',b).bind("click",function(c){c.preventDefault();var d=a(this);d.attr("disabled",!0).parent().addClass("saving");a.post(b.attr("action"),b.serialize(),function(){d.attr("disabled",!1).parent().removeClass("saving")})});var c=a("#mediaplayer div.howtouse").hide();a("#mediaplayer a.howtouse").bind("click",function(){c.slideToggle()})});
