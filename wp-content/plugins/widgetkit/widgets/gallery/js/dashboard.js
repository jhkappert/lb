/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){var b=a("#gallery form");a('input[type="submit"]',b).bind("click",function(d){d.preventDefault();var c=a(this);c.attr("disabled",!0).parent().addClass("saving");a.post(b.attr("action"),b.serialize(),function(){c.attr("disabled",!1).parent().removeClass("saving")})});a("#gallery").delegate("a.action.delete","click",function(b){b.preventDefault();if(confirm("Are you Sure?")){var c=a(this);a.post(widgetkitajax+"&task=delete_gallery",{id:a(this).attr("data-id")},function(b){b&&b.id?
c.parents("tr:first").fadeOut(function(){a(this).remove()}):alert("Delete action failed.")},"json")}})});
