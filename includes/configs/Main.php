<?php

namespace MerryCode\ColorBasedProductImport\Configs;

class Main
{
    public const TEXT_DOMAIN = "color-based-product-import";
    public const VERSION     = "1.0";

    // import statuses
    public const IMPORT_STATUS_IDLE         = "idle";
    public const IMPORT_STATUS_IN_PROGRESS  = "in-progress";
    public const IMPORT_STATUS_DONE         = "done";

    // prefixes
    public const STYLES_PREFIX  = "cbpi";
    public const SCRIPTS_PREFIX = "cbpi";

    // actions
    public const ACTION_PRODUCT_IMPORT = " cbpi_file_handle";

    // menu page
    public const MENU_CAPABILITY = "manage_options";
    public const MENU_SLUG       = "cbpi-options";
    public const MENU_ICON       = "dashicons-admin-generic";
    public const MENU_POSITION   = 6 ;

    // post commands and arguments
    public const POST_ARGUMENT_COMMAND          =  "cbpi_command" ;
    public const POST_ARGUMENT_VALUE_NEW_IMPORT =  "new_import";

    // import settings
    public const SETTINGS_KEY_STATUS          =  "status" ;

	public const ATTRIBUTE_SLUG_COLOR  = "pa_color";
	public const ATTRIBUTE_SLUG_COLOR_NO_PREFIX  = "color";
	public const ATTRIBUTE_SLUG_COLOR_NAME   = "Color";

	public const TERM_META_KEY = "cbpi_pa_color";



    public static function getMenuPageTitle() : string
    {
        return __('CBPI Options', static::TEXT_DOMAIN);
    }
}
