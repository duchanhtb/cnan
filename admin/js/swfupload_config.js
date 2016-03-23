var swfu;
window.onload = function () {
    if ($("#spanButtonPlaceholder").length) {
        swfu = new SWFUpload({
            // Backend Settings
            upload_url: admin_url + "upload-swf.php?type=" + type,
            post_params: {"PHPSESSID": session_id},
            // File Upload Settings
            file_size_limit: "5 MB", // 3MB
            file_types: "*.jpg",
            file_types_description: "JPG Images",
            file_upload_limit: "0",
            // Event Handler Settings - these functions as defined in Handlers.js
            //  The handlers are not part of SWFUpload but are part of my website and control how
            //  my website reacts to the SWFUpload events.
            file_queue_error_handler: fileQueueError,
            file_dialog_complete_handler: fileDialogComplete,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccess,
            upload_complete_handler: uploadComplete,
            // Button Settings
            //button_image_url : "images/button_Upload.png",
            button_placeholder_id: "spanButtonPlaceholder",
            button_width: 150,
            button_height: 25,
            button_text: '<span class="button">[Chọn ảnh Upload]</span>',
            button_text_style: '.button { font-family: Tahoma, Arial, sans-serif; font-size: 12pt; font-weight: bold; }',
            button_text_top_padding: 5,
            button_text_left_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            // Flash Settings
            flash_url: "swf/swfupload.swf",
            custom_settings: {
                upload_target: "divFileProgressContainer"
            },
            // Debug Settings
            debug: false
        });
    } // endif
};
