<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// set attributes
$attributes = array();
$attributes['type']   = 'text';
$attributes['name']   = $name;
$attributes['class']  = 'html';
$attributes['style']  = 'width:100%;min-height:150px;';

$attributes['id'] = $id = isset($attributes['id']) ? $attributes['id'] : 'html-'.uniqid();
?>

<div id="editor-<?php echo $id;?>" class="html-editor">

	<?php if(!$this["system"]->use_old_editor):?>


        <?php
            wp_editor( $value, $id, array(
                'wpautop' => true,
                'media_buttons' => true,
                'textarea_name' => $name,
                'textarea_rows' => 10,
                'editor_class' => "horizontal",
                'teeny' => false,
                'dfw' => false,
                'tinymce' => true,
                'quicktags' => true
            ));
        ?>


        <script type="text/javascript">

        	jQuery(function($){

        		var i = 0,
        			tinyParams = {},
        			editor_id  = "<?php echo $id;?>",
        			editor     = $("#editor-"+editor_id);


				if (editor.data("tinyfied")) {
					return;
				}

                for(var k in tinyMCEPreInit.mceInit){
                    if(i == 0) tinyParams = tinyMCEPreInit.mceInit[k];
                    i++;
                }
                tinyParams.mode = "exact";
                tinyParams.elements = editor_id;
                
                quicktags({
                    id: editor_id,
                    buttons: "",
                    disabled_buttons: ""
                });
                QTags._buttonsInit();
                jQuery('#wp-' + editor_id + '-wrap').removeClass('html-active').addClass('tmce-active');

                tinyMCE.init(tinyParams);

                tb_init(jQuery('#editor-' + editor_id + ' .horizontal-slide-media a.thickbox'));
                tb_init(jQuery('#editor-' + editor_id + ' .vertical-slide-media a.thickbox'));

             	if (editor.find('.quicktags-toolbar').length>1) {
             		editor.find('.quicktags-toolbar:first').remove();
             	}

                editor.find('.media-buttons a.thickbox').unbind('click').bind('click', function(){
            		if ( typeof tinyMCE != 'undefined' && tinyMCE.activeEditor ) {
			            var url = jQuery(e).attr('href');
			            url = url.split('editor=');
			            if(url.length>1){
			                url = url[1];
			                url = url.split('&');
			                if(url.length>1){
			                    editorid = url[0];
			                }
			            }
			            tinyMCE.get(editor_id).focus();
			            tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
			            jQuery(window).resize();
			        }
        		});

        		editor.bind("editor-action-start", function(){
					
					tinyMCE.execCommand('mceRemoveControl', false, editor_id);
					
				}).bind("editor-action-stop", function(){

					if (jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active')) {
						tinyMCE.execCommand('mceAddControl', true, editor_id);
					}
					
				}).data("tinyfied", true);
        	});
        </script>

	<?php else: ?>
		<div class="editortopbar">
			<div class="media-buttons hide-if-no-js" style="position:absolute;left:0px;top:0px;text-align:left;">
			<?php
				
				$media_upload_iframe_src = get_bloginfo( 'wpurl' ) . '/wp-admin/media-upload.php?post_id={widget_id}&amp;editor=1';
				
				$media = array(
					'image' => array(
						'title' => __('Add an Image'),
						'src'   => apply_filters( 'image_upload_iframe_src', $media_upload_iframe_src . "&amp;type=image" )
					),
					'video' => array(
						'title' => __('Add Video'),
						'src'   => apply_filters( 'image_upload_iframe_src', $media_upload_iframe_src . "&amp;type=video" )
					),
					'music' => array(
						'title' => __('Add Audio'),
						'src'   => apply_filters( 'image_upload_iframe_src', $media_upload_iframe_src . "&amp;type=audio" )
					),
					'other' => array(
						'title' => __('Add Media'),
						'src'   => $media_upload_iframe_src
					)
				);
				
				$out = array(apply_filters( 'media_buttons_context', __( 'Upload/Insert ' ) ));
				
				foreach ($media as $key => $info) {
				
					$out[] = '<a href="'.$info['src'].'&amp;TB_iframe=true&height=450&width=650" class="btnmedia" title="'.$info['title'].'"><img src="'.get_bloginfo('wpurl').'/wp-admin/images/media-button-'.$key.'.gif" alt="'.$info['title'].'" /></a>';
				}
				
				echo implode(" ", $out);
			?>
			</div>
			
			<div class="editortabs">
				<span class="visual active">Visual</span>
				<span class="html">Html</span>
			</div>
		</div>
		<div>
			<?php
				printf('<textarea %s>%s</textarea>', $this['field']->attributes($attributes, array('label', 'description', 'default')), $value);
			?>
		</div>

	<script type="text/javascript">
		
		jQuery(function($){
			
			var editor_id  = "<?php echo $id;?>";
			var editor     = $("#editor-"+editor_id);
			
			if (editor.data("tinyfied")) {
				return;
			}
			
			var tinyParams = tinyMCEPreInit.mceInit;
			var tabs       = editor.find(".editortabs > span");
			var widget_id  = ($("#widget_id").val() == "0" || $("#widget_id").val() == "" ) ? false : $("#widget_id").val();
			var mediabar   = editor.find(".media-buttons")[widget_id ? 'show' : 'hide']();
			
			if (widget_id) {
				
				mediabar.find('a.btnmedia').each(function(){
					
					var link = $(this);
					
					link.attr("href", link.attr("href").replace("{widget_id}", widget_id)); 
					
				}).unbind("click").bind("click", function(){

					tb_click.apply(this);				
					
					var link = $(this);
					
					if ( typeof tinyMCE != 'undefined' && tinyMCE.activeEditor ) {
						var url = 	link.attr('href');
						url = url.split('editor=');
			
						tinyMCE.get(editor_id).focus();
						tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
						jQuery(window).resize();
						
						//----
						
						window.send_to_editor = function(response) {
							
							var ed;

							if ( typeof(tinyMCE) != 'undefined' && ( ed = tinyMCE.get(editor_id) ) && !ed.isHidden() ) {
								ed.focus();
								if (tinymce.isIE)
									ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);

								if ( response.indexOf('[caption') === 0 ) {
									if ( ed.plugins.wpeditimage )
										response = ed.plugins.wpeditimage._do_shcode(response);
								} else if ( response.indexOf('[gallery') === 0 ) {
									if ( ed.plugins.wpgallery )
										response = ed.plugins.wpgallery._do_gallery(response);
								}

								ed.execCommand('mceInsertContent', false, response);

							} else if ( typeof edInsertContent == 'function' ) {
								edInsertContent(editorid, response);
							} else {
								jQuery( editorid ).val( jQuery( editorid ).val() + response );
							}

							tb_remove();
						}
						
						//---
					}
					
					return false;
				});
			}
			
			tinyParams.mode = "exact";
			tinyParams.apply_source_formatting = true;
			tinyParams.elements = editor_id;
			
			tinyMCE.init(tinyParams);
			
			var activeTab = "visual";
			
			
			tabs.bind("click", function(){
				
				tabs.removeClass("active");
				
				var tab = $(this).addClass("active");
				
				if (tab.hasClass("visual")) {
					tinyMCE.execCommand('mceAddControl', false, editor_id);
					activeTab = "visual";
					if(widget_id) mediabar.show();
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, editor_id);
					activeTab = "html";
					mediabar.hide();
				}
			});
			
			editor.bind("editor-action-start", function(){
				
				tinyMCE.execCommand('mceRemoveControl', false, editor_id);
				
			}).bind("editor-action-stop", function(){

				if (activeTab == "visual") {
					tinyMCE.execCommand('mceAddControl', true, editor_id);
				}
				
			});
			
			editor.find('.media-buttons a.thickbox').bind('click', function(){
				

			});
			
			editor.data("tinyfied", true);
		});
		
	</script>
	<?php endif; ?>
</div>