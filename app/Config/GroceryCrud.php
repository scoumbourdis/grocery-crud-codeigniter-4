<?php
namespace Config;

class GroceryCrud
{
    /**
     * For view all the languages go to the folder assets/grocery_crud/languages/
     * @var string
     */
    public $default_language = 'English';

    /**
     * There are only three choices: "uk-date" (dd/mm/yyyy), "us-date" (mm/dd/yyyy) or "sql-date" (yyyy-mm-dd)
     * @var string
     */
    public $date_format = 'uk-date';

    /**
     * The default per page when a user firstly see a list page
     * @var int
     */
    public $default_per_page = 10;

    /**
     * You can choose between 'ckeditor','tinymce' or 'markitup'
     * @var string
     */
    public $default_text_editor = 'ckeditor';

    /**
     * You can choose 'minimal' or 'full'
     * @var string
     */
    public $text_editor_type = 'full';

    /**
     * The character limiter at the list page, zero(0) value if you don't want character limiter at your list page
     * @var int
     */
    public $character_limiter = 30;

    /**
     * Having some options at the list paging. This is the default one that all the websites are using.
     * Make sure that the number of default_per_page variable is included to this array.
     * @var array
     */
    public $paging_options = ['10','25','50','100'];

    /**
     * Default theme for grocery CRUD. You can choose between 'flexigrid', 'datatables', 'bootstrap', 'bootstrap-v4'
     * @var string
     */
    public $default_theme = 'flexigrid';

    /**
     * The environment is important so we can have specific configurations for specific environments
     * @var string
     */
    public $environment = 'production';

    /**
     * Turn XSS clean into true in case you are exposing your CRUD into public. Please be aware that this is
     * stripping all the HTML and do not just trim the extra javascript
     * @var bool
     */
    public $xss_clean = false;

}
