<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class FLBasicformModule
 */
class FLBasicformModule extends FLBuilderModule {

    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Smart Marketing Form', 'fl-builder'),
            'description'   => __('Select your Smart Marketing Form. If have no form, please make new form.', 'fl-builder'),
            'category'		=> __('Basic', 'fl-builder'),
            'dir'           => FL_MODULE_EXAMPLES_DIR . 'basic-example/',
            'url'           => FL_MODULE_EXAMPLES_URL . 'basic-example/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'icon'          => 'button.svg',
        ));
    }
}


/*
* get all forms
*/
if(!function_exists('all_forms')){
    function all_forms(){
        global $wpdb;
        $prefix = $wpdb->prefix; 
        $form_table = $prefix . 'awe_forms';
        $querystr="select `form_name`, `row_id` from $form_table ORDER BY `row_id` DESC";
        $all_forms = $wpdb->get_results($querystr, OBJECT);
        $outputA = array();
        foreach($all_forms as $sF){
            $outputA[$sF->row_id] = $sF->form_name;
        }
        return $outputA;
    }    
}



/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLBasicformModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'fl-builder'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('Select Form', 'fl-builder'), // Section Title
                'fields'        => array( // Section Fields
                    'form_id'     => array(
                        'type'          => 'select',
                        'label'         => __('Smart Marketing Form', 'fl-builder'),
                        'default'       => '',
                        'options'       => all_forms(),
                        'class'         => 'smart-marketing-form',
                        'help'          => 'If you already not create any form, you have to create from from your Deshboard > Smart Marketing.'
                    )
                )
            )
        )
    )
));