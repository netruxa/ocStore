<?php echo $header; ?>
<div id="content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><i class="icon-exclamation-sign"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="box-heading">
      <h1><i class="icon-edit"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="box-content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <div class="buttons"><button type="submit" class="btn"><i class="icon-ok"></i> <?php echo $button_save; ?></button> <a href="<?php echo $cancel; ?>" class="btn"><i class="icon-remove"></i> <?php echo $button_cancel; ?></a></div>
        <div class="control-group">
          <label class="control-label" for="input-name"><span class="required">*</span> <?php echo $entry_name; ?></label>
          <div class="controls">
            <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="input-xxlarge" />
            <?php if ($error_name) { ?>
            <span class="error"><?php echo $error_name; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $entry_store; ?></div>
          <div class="controls">
            <label class="checkbox">
              <?php if (in_array(0, $manufacturer_store)) { ?>
              <input type="checkbox" name="manufacturer_store[]" value="0" checked="checked" />
              <?php echo $text_default; ?>
              <?php } else { ?>
              <input type="checkbox" name="manufacturer_store[]" value="0" />
              <?php echo $text_default; ?>
              <?php } ?>
            </label>
            <?php foreach ($stores as $store) { ?>
            <label class="checkbox">
              <?php if (in_array($store['store_id'], $manufacturer_store)) { ?>
              <input type="checkbox" name="manufacturer_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
              <?php echo $store['name']; ?>
              <?php } else { ?>
              <input type="checkbox" name="manufacturer_store[]" value="<?php echo $store['store_id']; ?>" />
              <?php echo $store['name']; ?>
              <?php } ?>
            </label>
            <?php } ?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="input-keyword"><?php echo $entry_keyword; ?></label>
          <div class="controls">
            <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" />
            <span class="help-block"><?php echo $help_keyword; ?></span></div>
        </div>
        <div class="control-group">
          <label class="control-label" for="input-name"><?php echo $entry_image; ?></label>
          <div class="controls">
            <div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" />
              <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
              <br />
              <a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a></div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
          <div class="controls">
            <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" class="input-mini" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" />
          </div>
          <div id="languages" class="htabs">
            <?php foreach ($languages as $language) { ?>
            <a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
            <?php } ?>
          </div>
          <?php foreach ($languages as $language) { ?>
          <div id="language<?php echo $language['language_id']; ?>">
            <div class="control-group">
                <label class="control-label" for="input-name"><?php echo $entry_seo_h1; ?></label>
                <div class="controls">
                  <input type="text" name="manufacturer_description[<?php echo $language['language_id']; ?>][seo_h1]" maxlength="255" size="100" value="<?php echo isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['seo_h1'] : ''; ?>" />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input-name"><?php echo $entry_seo_title; ?></label>
                <div class="controls">
                  <input type="text" name="manufacturer_description[<?php echo $language['language_id']; ?>][seo_title]" maxlength="255" size="100" value="<?php echo isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['seo_title'] : ''; ?>" />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input-name"><?php echo $entry_meta_keyword; ?></label>
                <div class="controls">
                  <input type="text" name="manufacturer_description[<?php echo $language['language_id']; ?>][meta_keyword]" maxlength="255" size="100" value="<?php echo isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['meta_keyword'] : ''; ?>" />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input-name"><?php echo $entry_meta_description; ?></label>
                <div class="controls">
                  <textarea name="manufacturer_description[<?php echo $language['language_id']; ?>][meta_description]" cols="100" rows="2"><?php echo isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input-name"><?php echo $entry_description; ?></label>
                <div class="controls">
                  <textarea name="manufacturer_description[<?php echo $language['language_id']; ?>][description]" id="description<?php echo $language['language_id']; ?>"><?php echo isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['description'] : ''; ?></textarea>
                </div>
              </div>
            </table>
          </div>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
CKEDITOR.replace('description<?php echo $language['language_id']; ?>', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
<?php } ?>
//--></script>
<script type="text/javascript"><!--
$('.help-inline .icon-question-sign').tooltip();
--></script>
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();

	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<?php echo $footer; ?>
