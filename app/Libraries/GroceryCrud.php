<?php namespace App\Libraries;
/**
 * PHP grocery CRUD
 *
 * A Codeigniter library that creates a CRUD automatically with just few lines of code.
 *
 * Copyright (C) 2010 - 2014  John Skoumbourdis.
 *
 * LICENSE
 *
 * Grocery CRUD is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	grocery CRUD
 * @copyright  	Copyright (c) 2010 through 2014, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @version    	2.0.0
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

// ------------------------------------------------------------------------

use Exception;

/**
 * grocery Field Types
 *
 * The types of the fields and the default reactions
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @license     https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @link		http://www.grocerycrud.com/documentation
 */
class grocery_CRUD_Field_Types
{
    /**
     * Gets the field types of the main table.
     * @return array
     */
    public function get_field_types()
    {
        if ($this->field_types !== null) {
            return $this->field_types;
        }

        $types	= array();
        foreach($this->basic_model->get_field_types_basic_table() as $field_info)
        {
            $field_info->required = !empty($this->required_fields) && in_array($field_info->name,$this->required_fields) ? true : false;

            $field_info->display_as =
                isset($this->display_as[$field_info->name]) ?
                    $this->display_as[$field_info->name] :
                    ucfirst(str_replace("_"," ",$field_info->name));

            if($this->change_field_type !== null && isset($this->change_field_type[$field_info->name]))
            {
                $field_type 			= $this->change_field_type[$field_info->name];

                if (isset($this->relation[$field_info->name])) {
                    $field_info->crud_type = "relation_".$field_type->type;
                } else {
                    $field_info->crud_type 	= $field_type->type;
                    $field_info->extras 	=  $field_type->extras;
                }

                $real_type				= $field_info->crud_type;
            }
            elseif(isset($this->relation[$field_info->name]))
            {
                $real_type				= 'relation';
                $field_info->crud_type 	= 'relation';
            }
            else
            {
                $real_type = $this->get_type($field_info);
                $field_info->crud_type = $real_type;
            }

            switch ($real_type) {
                case 'text':
                    $field_info->extras =
                        in_array($field_info->name, $this->set_texteditor) ? 'text_editor' : false;
                    break;

                case 'relation':
                case 'relation_readonly':
                    $field_info->extras 	= $this->relation[$field_info->name];
                    break;

                default:
                    if(empty($field_info->extras))
                        $field_info->extras = false;
                    break;
            }

            $types[$field_info->name] = $field_info;
        }

        if(!empty($this->relation_n_n))
        {
            foreach($this->relation_n_n as $field_name => $field_extras)
            {
                $is_read_only = $this->change_field_type !== null
                && isset($this->change_field_type[$field_name])
                && $this->change_field_type[$field_name]->type == 'readonly'
                    ? true : false;
                $field_info = (object)array();
                $field_info->name		= $field_name;
                $field_info->crud_type 	= $is_read_only ? 'readonly' : 'relation_n_n';
                $field_info->extras 	= $field_extras;
                $field_info->required	= !empty($this->required_fields) && in_array($field_name,$this->required_fields) ? true : false;;
                $field_info->display_as =
                    isset($this->display_as[$field_name]) ?
                        $this->display_as[$field_name] :
                        ucfirst(str_replace("_"," ",$field_name));

                $types[$field_name] = $field_info;
            }
        }

        if(!empty($this->add_fields))
            foreach($this->add_fields as $field_object)
            {
                $field_name = isset($field_object->field_name) ? $field_object->field_name : $field_object;

                if(!isset($types[$field_name]))//Doesn't exist in the database? Create it for the CRUD
                {
                    $extras = false;
                    if($this->change_field_type !== null && isset($this->change_field_type[$field_name]))
                    {
                        $field_type = $this->change_field_type[$field_name];
                        $extras 	=  $field_type->extras;
                    }

                    $field_info = (object)array(
                        'name' => $field_name,
                        'crud_type' => $this->change_field_type !== null && isset($this->change_field_type[$field_name]) ?
                            $this->change_field_type[$field_name]->type :
                            'string',
                        'display_as' => isset($this->display_as[$field_name]) ?
                            $this->display_as[$field_name] :
                            ucfirst(str_replace("_"," ",$field_name)),
                        'required'	=> !empty($this->required_fields) && in_array($field_name,$this->required_fields) ? true : false,
                        'extras'	=> $extras
                    );

                    $types[$field_name] = $field_info;
                }
            }

        if(!empty($this->edit_fields))
            foreach($this->edit_fields as $field_object)
            {
                $field_name = isset($field_object->field_name) ? $field_object->field_name : $field_object;

                if(!isset($types[$field_name]))//Doesn't exist in the database? Create it for the CRUD
                {
                    $extras = false;
                    if($this->change_field_type !== null && isset($this->change_field_type[$field_name]))
                    {
                        $field_type = $this->change_field_type[$field_name];
                        $extras 	=  $field_type->extras;
                    }

                    $field_info = (object)array(
                        'name' => $field_name,
                        'crud_type' => $this->change_field_type !== null && isset($this->change_field_type[$field_name]) ?
                            $this->change_field_type[$field_name]->type :
                            'string',
                        'display_as' => isset($this->display_as[$field_name]) ?
                            $this->display_as[$field_name] :
                            ucfirst(str_replace("_"," ",$field_name)),
                        'required'	=> in_array($field_name,$this->required_fields) ? true : false,
                        'extras'	=> $extras
                    );

                    $types[$field_name] = $field_info;
                }
            }

        $this->field_types = $types;

        return $this->field_types;
    }

    public function get_primary_key()
    {
        return $this->basic_model->get_primary_key();
    }

    /**
     * Get the html input for the specific field with the
     * current value
     *
     * @param object $field_info
     * @param string $value
     * @return object
     */
    protected function get_field_input($field_info, $value = null)
    {
        $real_type = $field_info->crud_type;

        $types_array = array(
            'integer',
            'text',
            'true_false',
            'string',
            'date',
            'datetime',
            'enum',
            'set',
            'relation',
            'relation_readonly',
            'relation_n_n',
            'hidden',
            'password',
            'readonly',
            'dropdown',
            'multiselect'
        );

        if (in_array($real_type,$types_array)) {
            /* A quick way to go to an internal method of type $this->get_{type}_input .
				 * For example if the real type is integer then we will use the method
				 * $this->get_integer_input
				 *  */
            $field_info->input = $this->{"get_".$real_type."_input"}($field_info,$value);
        }
        else
        {
            $field_info->input = $this->get_string_input($field_info,$value);
        }

        return $field_info;
    }

    protected function change_list_value($field_info, $value = null)
    {
        $real_type = $field_info->crud_type;

        switch ($real_type) {
            case 'hidden':
            case 'invisible':
            case 'integer':

                break;
            case 'true_false':
                if(is_array($field_info->extras) && array_key_exists($value,$field_info->extras)) {
                    $value = $field_info->extras[$value];
                } else if(isset($this->default_true_false_text[$value])) {
                    $value = $this->default_true_false_text[$value];
                }
                break;
            case 'string':
                $value = $this->character_limiter($value,$this->character_limiter,"...");
                break;
            case 'text':
                $value = $this->character_limiter(strip_tags($value),$this->character_limiter,"...");
                break;
            case 'date':
                if(!empty($value) && $value != '0000-00-00' && $value != '1970-01-01')
                {
                    list($year,$month,$day) = explode("-",$value);

                    $value = date($this->php_date_format, mktime (0, 0, 0, (int)$month , (int)$day , (int)$year));
                }
                else
                {
                    $value = '';
                }
                break;
            case 'datetime':
                if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '1970-01-01 00:00:00')
                {
                    list($year,$month,$day) = explode("-",$value);
                    list($hours,$minutes) = explode(":",substr($value,11));

                    $value = date($this->php_date_format." - H:i", mktime ((int)$hours , (int)$minutes , 0, (int)$month , (int)$day ,(int)$year));
                }
                else
                {
                    $value = '';
                }
                break;
            case 'enum':
                $value = $this->character_limiter($value,$this->character_limiter,"...");
                break;

            case 'multiselect':
                $value_as_array = array();
                foreach(explode(",",$value) as $row_value)
                {
                    $value_as_array[] = array_key_exists($row_value,$field_info->extras) ? $field_info->extras[$row_value] : $row_value;
                }
                $value = implode(",",$value_as_array);
                break;

            case 'relation_n_n':
                $value = $this->character_limiter(str_replace(',',', ',$value),$this->character_limiter,"...");
                break;

            case 'password':
                $value = '******';
                break;

            case 'dropdown':
                $value = array_key_exists($value,$field_info->extras) ? $field_info->extras[$value] : $value;
                break;

            default:
                $value = $this->character_limiter($value,$this->character_limiter,"...");
                break;
        }

        return $value;
    }

    /**
     * Character Limiter of codeigniter (I just don't want to load the helper )
     *
     * Limits the string based on the character count.  Preserves complete words
     * so the character count may not be exactly as specified.
     *
     * @access	public
     * @param	string
     * @param	integer
     * @param	string	the end character. Usually an ellipsis
     * @return	string
     */
    function character_limiter($str, $n = 500, $end_char = '&#8230;')
    {
        if (strlen($str) < $n)
        {
            return $str;
        }

        // a bit complicated, but faster than preg_replace with \s+
        $str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\x0B", "\x0C"), ' ', $str));

        if (strlen($str) <= $n)
        {
            return $str;
        }

        $out = '';
        foreach (explode(' ', trim($str)) as $val)
        {
            $out .= $val.' ';

            if (strlen($out) >= $n)
            {
                $out = trim($out);
                return (strlen($out) === strlen($str)) ? $out : $out.$end_char;
            }
        }
    }

    protected function get_type($db_type)
    {
        $type = false;
        if(!empty($db_type->type))
        {
            switch ($db_type->type) {
                case '1':
                case '3':
                case 'int':
                case 'tinyint':
                case 'mediumint':
                case 'longint':
                    if( $db_type->db_type == 'tinyint' && $db_type->db_max_length ==  1)
                        $type = 'true_false';
                    else
                        $type = 'integer';
                    break;
                case '254':
                case 'string':
                case 'enum':
                    if($db_type->db_type != 'enum')
                        $type = 'string';
                    else
                        $type = 'enum';
                    break;
                case 'set':
                    if($db_type->db_type != 'set')
                        $type = 'string';
                    else
                        $type = 'set';
                    break;
                case '252':
                case 'blob':
                case 'text':
                case 'mediumtext':
                case 'longtext':
                    $type = 'text';
                    break;
                case '10':
                case 'date':
                    $type = 'date';
                    break;
                case '12':
                case 'datetime':
                case 'timestamp':
                    $type = 'datetime';
                    break;
            }
        }
        return $type;
    }
}

// ------------------------------------------------------------------------

/**
 * Grocery Model Driver
 *
 * Drives the model - I'ts so easy like you drive a bicycle :-)
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @version    	2.0.0
 * @link		http://www.grocerycrud.com/documentation
 */
class grocery_CRUD_Model_Driver extends grocery_CRUD_Field_Types
{
    /**
     * @var Grocery_crud_model
     */
    public $basic_model = null;

    protected function set_default_Model()
    {
        $this->basic_model = new \App\Models\GroceryCrudModel();
    }

    protected function get_total_results()
    {
        $this->basic_model->setBuilder($this->basic_db_table);

        if(!empty($this->where))
            foreach($this->where as $where)
                $this->basic_model->where($where[0],$where[1],$where[2]);

        if(!empty($this->or_where))
            foreach($this->or_where as $or_where)
                $this->basic_model->or_where($or_where[0],$or_where[1],$or_where[2]);

        if(!empty($this->like))
            foreach($this->like as $like)
                $this->basic_model->like($like[0],$like[1],$like[2]);

        if(!empty($this->or_like))
            foreach($this->or_like as $or_like)
                $this->basic_model->or_like($or_like[0],$or_like[1],$or_like[2]);

        if(!empty($this->having))
            foreach($this->having as $having)
                $this->basic_model->having($having[0],$having[1],$having[2]);

        if(!empty($this->or_having))
            foreach($this->or_having as $or_having)
                $this->basic_model->or_having($or_having[0],$or_having[1],$or_having[2]);

        if(!empty($this->relation))
            foreach($this->relation as $relation)
                $this->basic_model->join_relation($relation[0],$relation[1],$relation[2]);

        if(!empty($this->relation_n_n))
        {
            $columns = $this->get_columns();
            foreach($columns as $column)
            {
                //Use the relation_n_n ONLY if the column is called . The set_relation_n_n are slow and it will make the table slower without any reason as we don't need those queries.
                if(isset($this->relation_n_n[$column->field_name]))
                {
                    $this->basic_model->set_relation_n_n_field($this->relation_n_n[$column->field_name]);
                }
            }

        }

        return $this->basic_model->get_total_results();
    }

    protected function filter_data_from_xss($post_data) {
        foreach ($post_data as $field_name => $rawData) {
            if (!is_array($rawData)) {
                $post_data[$field_name] = filter_var(strip_tags($rawData));
            }
        }
        return $post_data;
    }

    /**
     * @param object $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->basic_model = $model;

        return $this;
    }

    protected function set_ajax_list_queries($state_info = null)
    {
        $field_types = $this->get_field_types();

        if(!empty($state_info->per_page))
        {
            if(empty($state_info->page) || !is_numeric($state_info->page) )
                $this->limit($state_info->per_page);
            else
            {
                $limit_page = ( ($state_info->page-1) * $state_info->per_page );
                $this->limit($state_info->per_page, $limit_page);
            }
        }

        if(!empty($state_info->order_by))
        {
            $this->defaultOrdering($state_info->order_by[0],$state_info->order_by[1]);
        }

        if(isset($state_info->search) && $state_info->search !== '')
        {
            if (!empty($this->relation)) {
                foreach ($this->relation as $relation_name => $relation_values) {
                    $temp_relation[$this->_unique_field_name($relation_name)] = $this->_get_field_names_to_search($relation_values);
                }
            }

            if (is_array($state_info->search)) {
                foreach ($state_info->search as $search_field => $search_text) {


                    if (isset($temp_relation[$search_field])) {
                        if (is_array($temp_relation[$search_field])) {
                            $temp_where_query_array = [];

                            foreach ($temp_relation[$search_field] as $relation_field) {
                                $escaped_text = $this->basic_model->escape_str($search_text);
                                $temp_where_query_array[] = $relation_field . ' LIKE \'%' . $escaped_text . '%\'';
                            }
                            if (!empty($temp_where_query_array)) {
                                $this->where('(' . implode(' OR ', $temp_where_query_array) . ')', null);
                            }

                        } else {
                            $this->like($temp_relation[$search_field] , $search_text);
                        }
                    } elseif(isset($this->relation_n_n[$search_field])) {
                        $escaped_text = $this->basic_model->escape_str($search_text);
                        $this->having($search_field." LIKE '%".$escaped_text."%'");
                    } else {
                        $this->like($search_field, $search_text);
                    }



                }
            } elseif ($state_info->search->field !== null) {
                if (isset($temp_relation[$state_info->search->field])) {
                    if (is_array($temp_relation[$state_info->search->field])) {
                        foreach ($temp_relation[$state_info->search->field] as $search_field) {
                            $this->or_like($search_field , $state_info->search->text);
                        }
                    } else {
                        $this->like($temp_relation[$state_info->search->field] , $state_info->search->text);
                    }
                } elseif(isset($this->relation_n_n[$state_info->search->field])) {
                    $escaped_text = $this->basic_model->escape_str($state_info->search->text);
                    $this->having($state_info->search->field." LIKE '%".$escaped_text."%'");
                } else {
                    $this->like($state_info->search->field , $state_info->search->text);
                }
            }
            // Search all field
            else
            {
                $columns = $this->get_columns();

                $search_text = $state_info->search->text;

                if(!empty($this->where))
                    foreach($this->where as $where)
                        $this->basic_model->having($where[0],$where[1],$where[2]);

                $temp_where_query_array = [];
                $basic_table = $this->get_table();

                foreach($columns as $column)
                {
                    if(isset($temp_relation[$column->field_name]))
                    {
                        if(is_array($temp_relation[$column->field_name]))
                        {
                            foreach($temp_relation[$column->field_name] as $search_field)
                            {
                                $escaped_text = $this->basic_model->escape_str($search_text);
                                $temp_where_query_array[] = $search_field . ' LIKE \'%' . $escaped_text . '%\'';
                            }
                        }
                        else
                        {
                            $escaped_text = $this->basic_model->escape_str($search_text);
                            $temp_where_query_array[] = $temp_relation[$column->field_name] . ' LIKE \'%' . $escaped_text . '%\'';
                        }
                    }
                    elseif(isset($this->relation_n_n[$column->field_name]))
                    {
                        //@todo have a where for the relation_n_n statement
                    }
                    elseif (
                        isset($field_types[$column->field_name]) &&
                        !in_array($field_types[$column->field_name]->type, array('date', 'datetime', 'timestamp'))
                    ) {
                        $escaped_text = $this->basic_model->escape_str($search_text);
                        $temp_where_query_array[] =  '`' . $basic_table . '`.' . $column->field_name . ' LIKE \'%' . $escaped_text . '%\'';
                    }
                }

                if (!empty($temp_where_query_array)) {
                    $this->where('(' . implode(' OR ', $temp_where_query_array) . ')', null);
                }
            }
        }
    }

    protected function table_exists($table_name = null)
    {
        if($this->basic_model->db_table_exists($table_name))
            return true;
        return false;
    }

    protected function get_relation_array($relation_info, $primary_key_value = null, $limit = null)
    {
        list($field_name , $related_table , $related_field_title, $where_clause, $order_by)  = $relation_info;

        if($primary_key_value !== null)
        {
            $primary_key = $this->basic_model->get_primary_key($related_table);

            //A where clause with the primary key is enough to take the selected key row
            $where_clause = array($primary_key => $primary_key_value);
        }

        $relation_array = $this->basic_model->get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, $limit);

        return $relation_array;
    }

    protected function get_relation_total_rows($relation_info)
    {
        list($field_name , $related_table , $related_field_title, $where_clause)  = $relation_info;

        $relation_array = $this->basic_model->get_relation_total_rows($field_name , $related_table , $related_field_title, $where_clause);

        return $relation_array;
    }

    protected function db_insert_validation()
    {
        $validation_result = (object)array('success'=>false);

        $field_types = $this->get_field_types();
        $required_fields = $this->required_fields;
        $unique_fields = $this->_unique_fields;
        $add_fields = $this->get_add_fields();

        if(!empty($required_fields))
        {
            foreach($add_fields as $add_field)
            {
                $field_name = $add_field->field_name;

                // Workaround as Codeigniter set_rules has a bug with array and doesn't work with required fields.
                // We are basically doing the check here!
                if (array_key_exists($field_name, $this->relation_n_n) && in_array($field_name, $required_fields)) {
                    if (!array_key_exists($field_name, $_POST)) {
                        // This will always throw an error!
                        $this->setRule($field_name, $field_types[$field_name]->display_as, 'required');
                    }
                } else if(!isset($this->validation_rules[$field_name]) && in_array( $field_name, $required_fields) ) {
                    $this->setRule($field_name, $field_types[$field_name]->display_as, 'required');
                }
            }
        }

        /** Checking for unique fields. If the field value is not unique then
         * return a validation error straight away, if not continue... */
        if(!empty($unique_fields))
        {
            $form_validation = $this->form_validation();

            foreach($add_fields as $add_field)
            {
                $field_name = $add_field->field_name;
                if(in_array( $field_name, $unique_fields) )
                {
                    $form_validation->setRule( $field_name,
                        $field_types[$field_name]->display_as,
                        'is_unique['.$this->basic_db_table.'.'.$field_name.']');
                }
            }

            if(!$form_validation->run($_POST))
            {
                $errorFields = $form_validation->getErrors();
                $validation_result->error_fields = $errorFields;
                $validation_result->error_message = $this->errorStringFromArray($errorFields);

                return $validation_result;
            }
        }

        if(!empty($this->validation_rules))
        {
            $form_validation = $this->form_validation();

            $add_fields = $this->get_add_fields();

            foreach($add_fields as $add_field)
            {
                $field_name = $add_field->field_name;
                if(isset($this->validation_rules[$field_name]))
                {
                    $rule = $this->validation_rules[$field_name];
                    $form_validation->setRule($rule['field'],$rule['label'],$rule['rules'],$rule['errors']);
                }
            }

            if($form_validation->run($_POST))
            {
                $validation_result->success = true;
            }
            else
            {
                $errorFields = $form_validation->getErrors();
                $validation_result->error_fields = $errorFields;
                $validation_result->error_message = $this->errorStringFromArray($errorFields);
            }
        }
        else
        {
            $validation_result->success = true;
        }

        return $validation_result;
    }

    protected function form_validation()
    {
        if($this->form_validation === null) {
            $this->form_validation = \Config\Services::validation();
        }
        return $this->form_validation;
    }

    protected function db_update_validation()
    {
        $validation_result = (object)array('success'=>false);

        $field_types = $this->get_field_types();
        $required_fields = $this->required_fields;
        $unique_fields = $this->_unique_fields;
        $edit_fields = $this->get_edit_fields();

        if(!empty($required_fields))
        {
            foreach($edit_fields as $edit_field)
            {
                $field_name = $edit_field->field_name;

                // Workaround as Codeigniter setRule has a bug with array and doesn't work with required fields.
                // We are basically doing the check here!
                if (array_key_exists($field_name, $this->relation_n_n) && in_array($field_name, $required_fields)) {
                    if (!array_key_exists($field_name, $_POST)) {
                        // This will always throw an error!
                        $this->setRule($field_name, $field_types[$field_name]->display_as, 'required');
                    }
                } else if(!isset($this->validation_rules[$field_name]) && in_array( $field_name, $required_fields) ) {
                    $this->setRule($field_name, $field_types[$field_name]->display_as, 'required');
                }


            }
        }


        /** Checking for unique fields. If the field value is not unique then
         * return a validation error straight away, if not continue... */
        if(!empty($unique_fields))
        {
            $form_validation = $this->form_validation();

            $form_validation_check = false;

            foreach($edit_fields as $edit_field)
            {
                $field_name = $edit_field->field_name;
                if(in_array( $field_name, $unique_fields) )
                {
                    $state_info = $this->getStateInfo();
                    $primary_key = $this->get_primary_key();
                    $field_name_value = $_POST[$field_name];

                    $this->basic_model->setBuilder($this->basic_db_table);
                    $this->basic_model->where($primary_key, $state_info->primary_key);
                    $row = $this->basic_model->get_row();

                    if(!property_exists($row, $field_name)) {
                        throw new Exception("The field name doesn't exist in the database. ".
                            "Please use the unique fields only for fields that exist in the database");
                    }

                    $previous_field_name_value = $row->$field_name;

                    // Only check if the value is unique on change
                    if(!empty($previous_field_name_value) && $previous_field_name_value != $field_name_value) {
                        $form_validation->setRule($field_name,
                            $field_types[$field_name]->display_as,
                            'is_unique['.$this->basic_db_table.'.'.$field_name.']');

                        $form_validation_check = true;
                    }
                }
            }

            if($form_validation_check && !$form_validation->run($_POST))
            {
                $errorFields = $form_validation->getErrors();
                $validation_result->error_fields = $errorFields;
                $validation_result->error_message = $this->errorStringFromArray($errorFields);

                return $validation_result;
            }
        }

        if(!empty($this->validation_rules))
        {
            $form_validation = $this->form_validation();

            $edit_fields = $this->get_edit_fields();

            foreach($edit_fields as $edit_field)
            {
                $field_name = $edit_field->field_name;
                if(isset($this->validation_rules[$field_name]))
                {
                    $rule = $this->validation_rules[$field_name];
                    $form_validation->setRule($rule['field'], $rule['label'], $rule['rules'], $rule['errors']);
                }
            }

            if($form_validation->run($_POST))
            {
                $validation_result->success = true;
            }
            else
            {
                $errorFields = $form_validation->getErrors();
                $validation_result->error_fields = $errorFields;
                $validation_result->error_message = $this->errorStringFromArray($errorFields);

            }
        }
        else
        {
            $validation_result->success = true;
        }

        return $validation_result;
    }

    protected function errorStringFromArray($errors) {
        $finalError = '';

        foreach ($errors as $error_message) {
            $finalError .= '<p>' . $error_message . '</p>';
        }

        return $finalError;
    }

    protected function db_insert($state_info)
    {
        $validation_result = $this->db_insert_validation();

        if($validation_result->success)
        {
            $post_data = $state_info->unwrapped_data;

            if ($this->config->xss_clean) {
                $post_data = $this->filter_data_from_xss($post_data);
            }

            $add_fields = $this->get_add_fields();

            if($this->callback_insert === null)
            {
                if($this->callback_before_insert !== null)
                {
                    $stateParameters = (object)[
                        'data' => $post_data
                    ];
                    $callback_return = call_user_func($this->callback_before_insert, $stateParameters);

                    if(!empty($callback_return) && is_object($callback_return)) {
                        $post_data = $stateParameters->data;
                    } elseif($callback_return === false) {
                        return false;
                    }
                }

                $insert_data = array();
                $types = $this->get_field_types();
                foreach($add_fields as $num_row => $field)
                {
                    /* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
                    if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
                    {
                        $post_data[$field->field_name] = array();
                    }

                    if(array_key_exists($field->field_name, $post_data) && !isset($this->relation_n_n[$field->field_name]))
                    {
                        if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
                        {
                            $insert_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
                        {
                            $insert_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
                        {
                            $insert_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
                        {
                            //This empty if statement is to make sure that a readonly field will never inserted/updated
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
                        {
                            $insert_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
                            $insert_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
                                substr($post_data[$field->field_name],10);
                        }
                        else
                        {
                            $insert_data[$field->field_name] = $post_data[$field->field_name];
                        }
                    }
                }

                $insert_result =  $this->basic_model->db_insert($insert_data);

                if($insert_result !== false)
                {
                    $insert_primary_key = $insert_result;
                }
                else
                {
                    return false;
                }

                if(!empty($this->relation_n_n))
                {
                    foreach($this->relation_n_n as $field_name => $field_info)
                    {
                        $relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
                        $this->db_relation_n_n_update($field_info, $relation_data  ,$insert_primary_key);
                    }
                }

                if($this->callback_after_insert !== null)
                {
                    $stateParameters = (object)[
                        'data' => $post_data,
                        'insertId' => $insert_primary_key
                    ];

                    $callback_return = call_user_func($this->callback_after_insert, $stateParameters);

                    if($callback_return === false)
                    {
                        return false;
                    }

                }
            }else
            {
                $stateParameters = (object)[
                    'data' => $post_data
                ];
                $callback_return = call_user_func($this->callback_insert, $stateParameters);

                $insert_primary_key = property_exists($stateParameters, 'insertId')
                    ? $stateParameters->insertId : null;

                if($callback_return === false) {
                    return false;
                }

                return $insert_primary_key;
            }

            if(isset($insert_primary_key)) {
                return $insert_primary_key;
            }

            return true;
        }

        return false;

    }

    protected function db_update($state_info)
    {
        $validation_result = $this->db_update_validation();

        $edit_fields = $this->get_edit_fields();

        if($validation_result->success)
        {
            $post_data 		= $state_info->unwrapped_data;
            $primary_key 	= $state_info->primary_key;

            if ($this->config->xss_clean) {
                $post_data = $this->filter_data_from_xss($post_data);
            }

            if($this->callback_update === null)
            {
                if($this->callback_before_update !== null)
                {
                    $stateParameters = (object)[
                        'primaryKeyValue' => $primary_key,
                        'data' => $post_data
                    ];
                    $callbackReturn = call_user_func($this->callback_before_update, $stateParameters);

                    if(!empty($callbackReturn) && is_object($callbackReturn)) {
                        $post_data = $callbackReturn->data;
                    } elseif($callbackReturn === false) {
                        return false;
                    }

                }

                $update_data = array();
                $types = $this->get_field_types();
                foreach($edit_fields as $num_row => $field)
                {
                    /* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
                    if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
                    {
                        $post_data[$field->field_name] = array();
                    }

                    if(array_key_exists($field->field_name, $post_data) && !isset($this->relation_n_n[$field->field_name]))
                    {
                        if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
                        {
                            $update_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
                        {
                            $update_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
                        {
                            $update_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
                        {
                            //This empty if statement is to make sure that a readonly field will never inserted/updated
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
                        {
                            $update_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
                            $update_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
                                substr($post_data[$field->field_name],10);
                        }
                        else
                        {
                            $update_data[$field->field_name] = $post_data[$field->field_name];
                        }
                    }
                }

                if($this->basic_model->db_update($update_data, $primary_key) === false)
                {
                    return false;
                }

                if(!empty($this->relation_n_n))
                {
                    foreach($this->relation_n_n as $field_name => $field_info)
                    {
                        if (   $this->unset_edit_fields !== null
                            && is_array($this->unset_edit_fields)
                            && in_array($field_name,$this->unset_edit_fields)
                        ) {
                            continue;
                        }

                        $relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
                        $this->db_relation_n_n_update($field_info, $relation_data ,$primary_key);
                    }
                }

                if($this->callback_after_update !== null)
                {
                    $stateParameters = (object)[
                        'primaryKeyValue' => $primary_key,
                        'data' => $post_data
                    ];

                    $callbackReturn = call_user_func($this->callback_after_update, $stateParameters);

                    if($callbackReturn === false)
                    {
                        return false;
                    }

                }
            }
            else
            {
                $stateParameters = (object)[
                    'primaryKeyValue' => $primary_key,
                    'data' => $post_data
                ];

                $callbackReturn = call_user_func($this->callback_update, $stateParameters);

                if($callbackReturn === false)
                {
                    return false;
                }
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    protected function _convert_date_to_sql_date($date)
    {
        $date = substr($date,0,10);
        if(preg_match('/\d{4}-\d{2}-\d{2}/',$date))
        {
            //If it's already a sql-date don't convert it!
            return $date;
        }elseif(empty($date))
        {
            return '';
        }

        $date_array = preg_split( '/[-\.\/ ]/', $date);
        if($this->php_date_format == 'd/m/Y')
        {
            $sql_date = date('Y-m-d',mktime(0,0,0,$date_array[1],$date_array[0],$date_array[2]));
        }
        elseif($this->php_date_format == 'm/d/Y')
        {
            $sql_date = date('Y-m-d',mktime(0,0,0,$date_array[0],$date_array[1],$date_array[2]));
        }
        else
        {
            $sql_date = $date;
        }

        return $sql_date;
    }

    protected function _get_field_names_to_search(array $relation_values)
    {
        if(!strstr($relation_values[2],'{')) {
            return $this->_unique_join_name($relation_values[0]).'.'.$relation_values[2];
        } else {
            $relation_values[2] = ' '.$relation_values[2].' ';
            $temp1 = explode('{',$relation_values[2]);
            unset($temp1[0]);

            $field_names_array = array();
            foreach($temp1 as $field) {
                list($field_name) = explode('}',$field);
                $field_name = $this->_unique_join_name($relation_values[0]).'.'. $field_name;
                $field_names_array[] = $field_name;
            }

            return $field_names_array;
        }
    }

    protected function _unique_join_name($field_name)
    {
        return 'j'.substr(md5($field_name),0,8); //This j is because is better for a string to begin with a letter and not a number
    }

    protected function _unique_field_name($field_name)
    {
        return 's'.substr(md5($field_name),0,8); //This s is because is better for a string to begin with a letter and not a number
    }

    protected function db_multiple_delete($state_info)
    {
        foreach ($state_info->ids as $delete_id) {
            $result = $this->db_delete((object)array('primary_key' => $delete_id));
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    protected function db_delete($state_info)
    {
        $primaryKeyValue 	= $state_info->primary_key;

        $stateParameters = (object)[
            'primaryKeyValue' => $state_info->primary_key
        ];

        if($this->callback_delete === null)
        {
            if($this->callback_before_delete !== null)
            {
                $callback_return = call_user_func($this->callback_before_delete, $stateParameters);

                if($callback_return === false)
                {
                    return false;
                }

                if (is_object($callback_return)) {
                    $stateParameters = $callback_return;
                    $primaryKeyValue = $stateParameters->primaryKeyValue;
                }

            }

            if(!empty($this->relation_n_n))
            {
                foreach($this->relation_n_n as $field_name => $field_info)
                {
                    $this->db_relation_n_n_delete( $field_info, $primaryKeyValue );
                }
            }

            $delete_result = $this->basic_model->db_delete($primaryKeyValue);

            if($delete_result === false)
            {
                return false;
            }

            if($this->callback_after_delete !== null)
            {
                $callback_return = call_user_func($this->callback_after_delete, $stateParameters);

                if($callback_return === false)
                {
                    return false;
                }

            }
        }
        else
        {
            $callback_return = call_user_func($this->callback_delete, $stateParameters);

            if($callback_return === false)
            {
                return false;
            }
        }

        return true;
    }

    protected function db_relation_n_n_update($field_info, $post_data , $primary_key_value)
    {
        $this->basic_model->db_relation_n_n_update($field_info, $post_data , $primary_key_value);
    }

    protected function db_relation_n_n_delete($field_info, $primary_key_value)
    {
        $this->basic_model->db_relation_n_n_delete($field_info, $primary_key_value);
    }

    protected function get_list()
    {
        $this->basic_model->setBuilder($this->basic_db_table);

        if(!empty($this->order_by)) {
            $this->basic_model->order_by($this->order_by[0], $this->order_by[1]);
        }

        if(!empty($this->where)) {
            foreach($this->where as $where) {
                $this->basic_model->where($where[0],$where[1],$where[2]);
            }

        }

        if(!empty($this->or_where)) {
            foreach($this->or_where as $or_where) {
                $this->basic_model->or_where($or_where[0],$or_where[1],$or_where[2]);
            }
        }

        if(!empty($this->like))
            foreach($this->like as $like)
                $this->basic_model->like($like[0],$like[1],$like[2]);

        if(!empty($this->or_like))
            foreach($this->or_like as $or_like)
                $this->basic_model->or_like($or_like[0],$or_like[1],$or_like[2]);

        if(!empty($this->having))
            foreach($this->having as $having)
                $this->basic_model->having($having[0],$having[1],$having[2]);

        if(!empty($this->or_having))
            foreach($this->or_having as $or_having)
                $this->basic_model->or_having($or_having[0],$or_having[1],$or_having[2]);

        if(!empty($this->relation))
            foreach($this->relation as $relation)
                $this->basic_model->join_relation($relation[0],$relation[1],$relation[2]);

        if(!empty($this->relation_n_n))
        {
            $columns = $this->get_columns();
            foreach($columns as $column)
            {
                //Use the relation_n_n ONLY if the column is called . The set_relation_n_n are slow and it will make the table slower without any reason as we don't need those queries.
                if(isset($this->relation_n_n[$column->field_name]))
                {
                    $this->basic_model->set_relation_n_n_field($this->relation_n_n[$column->field_name]);
                }
            }

        }

        if($this->theme_config['crud_paging'] === true)
        {
            if($this->limit === null)
            {
                $default_per_page = $this->config->default_per_page;
                if(is_numeric($default_per_page) && $default_per_page >1)
                {
                    $this->basic_model->limit($default_per_page);
                }
                else
                {
                    $this->basic_model->limit(10);
                }
            }
            else
            {
                $this->basic_model->limit($this->limit[0],$this->limit[1]);
            }
        }

        $results = $this->basic_model->get_list();

        return $results;
    }

    protected function get_edit_values($primary_key_value)
    {
        $this->basic_model->setBuilder($this->basic_db_table);
        $values = $this->basic_model->get_edit_values($primary_key_value);

        if(!empty($this->relation_n_n))
        {
            foreach($this->relation_n_n as $field_name => $field_info)
            {
                $values->$field_name = $this->get_relation_n_n_selection_array($primary_key_value, $field_info);
            }
        }

        return $values;
    }

    protected function get_clone_values($primary_key_value)
    {
        $this->basic_model->setBuilder($this->basic_db_table);
        $values = $this->basic_model->get_edit_values($primary_key_value);

        if(!empty($this->relation_n_n)) {
            foreach($this->relation_n_n as $field_name => $field_info) {
                $values->$field_name = $this->get_relation_n_n_selection_array($primary_key_value, $field_info);
            }
        }

        return $values;
    }


    protected function get_relation_n_n_selection_array($primary_key_value, $field_info)
    {
        return $this->basic_model->get_relation_n_n_selection_array($primary_key_value, $field_info);
    }

    protected function get_relation_n_n_unselected_array($field_info, $selected_values)
    {
        return $this->basic_model->get_relation_n_n_unselected_array($field_info, $selected_values);
    }

    protected function set_basic_db_table($table_name = null)
    {
        $this->basic_model->set_basic_table($table_name);
    }

    protected function ajax_relation($state_info)
    {
        if(!isset($this->relation[$state_info->field_name]))
            return false;

        list($field_name, $related_table, $related_field_title, $where_clause, $order_by)  = $this->relation[$state_info->field_name];

        return $this->basic_model->get_ajax_relation_array($state_info->search, $field_name, $related_table, $related_field_title, $where_clause, $order_by);
    }
}


/**
 * PHP grocery CRUD
 *
 * LICENSE
 *
 * Grocery CRUD is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	grocery CRUD
 * @copyright  	Copyright (c) 2010 through 2014, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

// ------------------------------------------------------------------------

/**
 * PHP grocery Layout
 *
 * Here you manage all the HTML Layout
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @version    	2.0.0
 */
class grocery_CRUD_Layout extends grocery_CRUD_Model_Driver
{
    private $theme_path 				= null;
    private $views_as_string			= '';
    private $echo_and_die				= false;
    protected $theme 					= null;
    protected $default_true_false_text 	= array('inactive' , 'active');

    protected $css_files				= array();
    protected $js_files					= array();
    protected $js_lib_files				= array();
    protected $js_config_files			= array();

    protected function set_basic_Layout()
    {
        if(!file_exists($this->theme_path.$this->theme.'/views/list_template.php'))
        {
            throw new Exception('The template does not exist. Please check your files and try again.', 12);
            die();
        }
    }

    protected function showList($ajax = false, $state_info = null)
    {
        $data = $this->get_common_data();

        $data->order_by 	= $this->order_by;

        $data->types 		= $this->get_field_types();

        $data->list = $this->get_list();
        $data->list = $this->change_list($data->list , $data->types);
        $data->list = $this->change_list_add_actions($data->list);

        $data->total_results = $this->get_total_results();

        $data->columns 				= $this->get_columns();

        $data->success_message		= $this->get_success_message_at_list($state_info);

        $data->primary_key 			= $this->get_primary_key();
        $data->add_url				= $this->getAddUrl();
        $data->edit_url				= $this->getEditUrl();
        $data->clone_url			= $this->getCloneUrl();
        $data->delete_url			= $this->getDeleteUrl();
        $data->delete_multiple_url	= $this->getDeleteMultipleUrl();
        $data->read_url				= $this->getReadUrl();
        $data->ajax_list_url		= $this->getAjaxListUrl();
        $data->ajax_list_info_url	= $this->getAjaxListInfoUrl();
        $data->export_url			= $this->getExportToExcelUrl();
        $data->print_url			= $this->getPrintUrl();
        $data->actions				= $this->actions;
        $data->unique_hash			= $this->get_method_hash();
        $data->order_by				= $this->order_by;

        $data->unset_add			= $this->unset_add;
        $data->unset_edit			= $this->unset_edit;
        $data->unset_clone			= $this->unset_clone;
        $data->unset_read			= $this->unset_read;
        $data->unset_delete			= $this->unset_delete;
        $data->unset_export			= $this->unset_export;
        $data->unset_print			= $this->unset_print;

        $data->codeigniter4         = true;
        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $default_per_page = $this->config->default_per_page;
        $data->paging_options = $this->config->paging_options;
        $data->default_per_page		= is_numeric($default_per_page) && $default_per_page >1 && in_array($default_per_page,$data->paging_options)? $default_per_page : 25;

        if($data->list === false)
        {
            throw new Exception('It is impossible to get data. Please check your model and try again.', 13);
            $data->list = array();
        }

        foreach($data->list as $num_row => $row)
        {
            $data->list[$num_row]->primary_key_value = $row->{$data->primary_key};
            $data->list[$num_row]->edit_url = $data->edit_url.'/'.$row->{$data->primary_key};
            $data->list[$num_row]->delete_url = $data->delete_url.'/'.$row->{$data->primary_key};
            $data->list[$num_row]->read_url = $data->read_url.'/'.$row->{$data->primary_key};
            $data->list[$num_row]->clone_url = $data->clone_url.'/'.$row->{$data->primary_key};
        }

        if(!$ajax)
        {
            $data->list_view = $this->_theme_view('list.php',$data,true);
            $this->_theme_view('list_template.php',$data);
        }
        else
        {
            $this->set_echo_and_die();
            $this->_theme_view('list.php',$data);
        }
    }

    protected function exportToExcel($state_info = null)
    {
        $data = $this->get_common_data();

        $data->order_by 	= $this->order_by;
        $data->types 		= $this->get_field_types();

        $data->list = $this->get_list();
        $data->list = $this->change_list($data->list , $data->types);
        $data->list = $this->change_list_add_actions($data->list);

        $data->total_results = $this->get_total_results();

        $data->columns 				= $this->get_columns();
        $data->primary_key 			= $this->get_primary_key();

        @ob_end_clean();
        $this->_export_to_excel($data);
    }

    protected function _export_to_excel($data)
    {
        /**
         * No need to use an external library here. The only bad thing without using external library is that Microsoft Excel is complaining
         * that the file is in a different format than specified by the file extension. If you press "Yes" everything will be just fine.
         * */

        $string_to_export = "";
        foreach($data->columns as $column){
            $string_to_export .= $column->display_as."\t";
        }
        $string_to_export .= "\n";

        foreach($data->list as $num_row => $row){
            foreach($data->columns as $column){
                $string_to_export .= $this->_trim_export_string($row->{$column->field_name})."\t";
            }
            $string_to_export .= "\n";
        }

        // Convert to UTF-16LE and Prepend BOM
        $string_to_export = "\xFF\xFE" .mb_convert_encoding($string_to_export, 'UTF-16LE', 'UTF-8');

        $filename = "export-".date("Y-m-d_H:i:s").".xls";

        header('Content-type: application/vnd.ms-excel;charset=UTF-16LE');
        header('Content-Disposition: attachment; filename='.$filename);
        header("Cache-Control: no-cache");
        echo $string_to_export;
        die();
    }

    protected function print_webpage($state_info = null)
    {
        $data = $this->get_common_data();

        $data->order_by 	= $this->order_by;
        $data->types 		= $this->get_field_types();

        $data->list = $this->get_list();
        $data->list = $this->change_list($data->list , $data->types);
        $data->list = $this->change_list_add_actions($data->list);

        $data->total_results = $this->get_total_results();

        $data->columns 				= $this->get_columns();
        $data->primary_key 			= $this->get_primary_key();

        @ob_end_clean();
        $this->_print_webpage($data);
    }

    protected function _print_webpage($data)
    {
        $string_to_print = "<meta charset=\"utf-8\" /><style type=\"text/css\" >
		#print-table{ color: #000; background: #fff; font-family: Verdana,Tahoma,Helvetica,sans-serif; font-size: 13px;}
		#print-table table tr td, #print-table table tr th{ border: 1px solid black; border-bottom: none; border-right: none; padding: 4px 8px 4px 4px}
		#print-table table{ border-bottom: 1px solid black; border-right: 1px solid black}
		#print-table table tr th{text-align: left;background: #ddd}
		#print-table table tr:nth-child(odd){background: #eee}
		</style>";
        $string_to_print .= "<div id='print-table'>";

        $string_to_print .= '<table width="100%" cellpadding="0" cellspacing="0" ><tr>';
        foreach($data->columns as $column){
            $string_to_print .= "<th>".$column->display_as."</th>";
        }
        $string_to_print .= "</tr>";

        foreach($data->list as $num_row => $row){
            $string_to_print .= "<tr>";
            foreach($data->columns as $column){
                $string_to_print .= "<td>".$this->_trim_print_string($row->{$column->field_name})."</td>";
            }
            $string_to_print .= "</tr>";
        }

        $string_to_print .= "</table></div>";

        echo $string_to_print;
        die();
    }

    protected function _trim_export_string($value)
    {
        $value = str_replace(array("&nbsp;","&amp;","&gt;","&lt;"),array(" ","&",">","<"),$value);
        return  strip_tags(str_replace(array("\t","\n","\r"),"",$value));
    }

    protected function _trim_print_string($value)
    {
        $value = str_replace(array("&nbsp;","&amp;","&gt;","&lt;"),array(" ","&",">","<"),$value);

        //If the value has only spaces and nothing more then add the whitespace html character
        if(str_replace(" ","",$value) == "")
            $value = "&nbsp;";

        return strip_tags($value);
    }

    protected function set_echo_and_die()
    {
        $this->echo_and_die = true;
    }

    protected function unset_echo_and_die()
    {
        $this->echo_and_die = false;
    }

    protected function showListInfo()
    {
        $this->set_echo_and_die();

        $total_results = (int)$this->get_total_results();
        @ob_end_clean();
        echo json_encode(array('total_results' => $total_results));
        die();
    }

    protected function change_list_add_actions($list)
    {
        if(empty($this->actions))
            return $list;

        $primary_key = $this->get_primary_key();

        foreach($list as $num_row => $row)
        {
            $actions_urls = array();
            foreach($this->actions as $unique_id => $action)
            {
                $actions_urls[$unique_id] = call_user_func($action->url_callback, $row->$primary_key, $row);
            }
            $row->action_urls = $actions_urls;
        }

        return $list;
    }

    protected function change_list($list,$types)
    {
        $primary_key = $this->get_primary_key();
        $has_callbacks = !empty($this->callback_column) ? true : false;
        $output_columns = $this->get_columns();
        foreach($list as $num_row => $row)
        {
            foreach($output_columns as $column)
            {
                $field_name 	= $column->field_name;
                $field_value 	= isset( $row->{$column->field_name} ) ? $row->{$column->field_name} : null;
                if( $has_callbacks && isset($this->callback_column[$field_name]) )
                    $list[$num_row]->$field_name = call_user_func($this->callback_column[$field_name], $field_value, $row);
                elseif(isset($types[$field_name]))
                    $list[$num_row]->$field_name = $this->change_list_value($types[$field_name] , $field_value);
                else
                    $list[$num_row]->$field_name = $field_value;
            }
        }

        return $list;
    }

    protected function showAddForm()
    {
        $this->set_js_lib($this->default_javascript_path.'/'.GroceryCrud::JQUERY);

        $data 				= $this->get_common_data();
        $data->types 		= $this->get_field_types();

        $data->list_url 		= $this->getListUrl();
        $data->insert_url		= $this->getInsertUrl();
        $data->validation_url	= $this->getValidationInsertUrl();
        $data->input_fields 	= $this->get_add_input_fields();

        $data->fields 			= $this->get_add_fields();
        $data->hidden_fields	= $this->get_add_hidden_fields();
        $data->unset_back_to_list	= $this->unset_back_to_list;
        $data->unique_hash			= $this->get_method_hash();
        $data->is_ajax 			= $this->_is_ajax();

        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $this->_theme_view('add.php',$data);
        $this->_inline_js("var js_date_format = '".$this->js_date_format."';");

        $this->_get_ajax_results();
    }

    protected function showCloneForm($state_info)
    {
        $this->set_js_lib($this->default_javascript_path.'/'.GroceryCrud::JQUERY);

        $data 				= $this->get_common_data();
        $data->types 		= $this->get_field_types();

        $data->field_values = $this->get_edit_values($state_info->primary_key);

        $data->add_url		= $this->getAddUrl();
        $data->list_url 	= $this->getListUrl();
        $data->update_url	= $this->getInsertUrl();
        $data->delete_url	= $this->getDeleteUrl($state_info);
        $data->read_url		= $this->getReadUrl($state_info->primary_key);
        $data->input_fields = $this->get_clone_input_fields($data->field_values);
        $data->unique_hash			= $this->get_method_hash();

        $data->fields 		= $this->get_clone_fields();
        $data->hidden_fields	= $this->get_edit_hidden_fields();
        $data->unset_back_to_list	= $this->unset_back_to_list;

        $data->validation_url	= $this->getValidationInsertUrl();
        $data->is_ajax 			= $this->_is_ajax();

        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $this->_theme_view('edit.php',$data);
        $this->_inline_js("var js_date_format = '".$this->js_date_format."';");

        $this->_get_ajax_results();
    }


    protected function showEditForm($state_info)
    {
        $this->set_js_lib($this->default_javascript_path.'/'.GroceryCrud::JQUERY);

        $data 				= $this->get_common_data();
        $data->types 		= $this->get_field_types();

        $data->field_values = $this->get_edit_values($state_info->primary_key);

        $data->add_url		= $this->getAddUrl();
        $data->list_url 	= $this->getListUrl();
        $data->update_url	= $this->getUpdateUrl($state_info);
        $data->delete_url	= $this->getDeleteUrl($state_info);
        $data->read_url		= $this->getReadUrl($state_info->primary_key);
        $data->input_fields = $this->get_edit_input_fields($data->field_values);
        $data->unique_hash			= $this->get_method_hash();

        $data->fields 		= $this->get_edit_fields();
        $data->hidden_fields	= $this->get_edit_hidden_fields();
        $data->unset_back_to_list	= $this->unset_back_to_list;

        $data->validation_url	= $this->getValidationUpdateUrl($state_info->primary_key);
        $data->is_ajax 			= $this->_is_ajax();

        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $this->_theme_view('edit.php',$data);
        $this->_inline_js("var js_date_format = '".$this->js_date_format."';");

        $this->_get_ajax_results();
    }

    protected function showReadForm($state_info)
    {
        $this->set_js_lib($this->default_javascript_path.'/'.GroceryCrud::JQUERY);

        $data 				= $this->get_common_data();
        $data->types 		= $this->get_field_types();

        $data->field_values = $this->get_edit_values($state_info->primary_key);

        $data->add_url		= $this->getAddUrl();

        $data->list_url 	= $this->getListUrl();
        $data->update_url	= $this->getUpdateUrl($state_info);
        $data->delete_url	= $this->getDeleteUrl($state_info);
        $data->read_url		= $this->getReadUrl($state_info->primary_key);
        $data->input_fields = $this->get_read_input_fields($data->field_values);
        $data->unique_hash			= $this->get_method_hash();

        $data->fields 		= $this->get_read_fields();
        $data->hidden_fields	= $this->get_edit_hidden_fields();
        $data->unset_back_to_list	= $this->unset_back_to_list;

        $data->validation_url	= $this->getValidationUpdateUrl($state_info->primary_key);
        $data->is_ajax 			= $this->_is_ajax();

        $data->jquery_js            = GroceryCrud::JQUERY;
        $data->grocery_crud_version = GroceryCrud::VERSION;
        $data->csrf_cookie_name     = '';

        $this->_theme_view('read.php',$data);
        $this->_inline_js("var js_date_format = '".$this->js_date_format."';");

        $this->_get_ajax_results();
    }

    protected function delete_layout($delete_result = true)
    {
        @ob_end_clean();
        if($delete_result === false)
        {
            $error_message = '<p>'.$this->l('delete_error_message').'</p>';

            echo json_encode(array('success' => $delete_result ,'error_message' => $error_message));
        }
        else
        {
            $success_message = '<p>'.$this->l('delete_success_message').'</p>';

            echo json_encode(array('success' => true , 'success_message' => $success_message));
        }
        $this->set_echo_and_die();
    }

    protected function get_success_message_at_list($field_info = null)
    {
        if($field_info !== null && isset($field_info->success_message) && $field_info->success_message)
        {
            if(!empty($field_info->primary_key) && !$this->unset_edit)
            {
                return $this->l('insert_success_message')." <a class='go-to-edit-form' href='".$this->getEditUrl($field_info->primary_key)."'>".$this->l('form_edit')." {$this->subject}</a> ";
            }
            else
            {
                return $this->l('insert_success_message');
            }
        }
        else
        {
            return null;
        }
    }

    protected function insert_layout($insert_result = false)
    {
        @ob_end_clean();
        if($insert_result === false)
        {
            echo json_encode(array('success' => false));
        }
        else
        {
            $success_message = '<p>'.$this->l('insert_success_message');

            if(!$this->unset_back_to_list && !empty($insert_result) && !$this->unset_edit)
            {
                $success_message .= " <a class='go-to-edit-form' href='".$this->getEditUrl($insert_result)."'>".$this->l('form_edit')." {$this->subject}</a> ";

                if (!$this->_is_ajax()) {
                    $success_message .= $this->l('form_or');
                }
            }

            if(!$this->unset_back_to_list && !$this->_is_ajax())
            {
                $success_message .= " <a href='".$this->getListUrl()."'>".$this->l('form_go_back_to_list')."</a>";
            }

            $success_message .= '</p>';

            echo json_encode(array(
                'success' => true ,
                'insert_primary_key' => $insert_result,
                'success_message' => $success_message,
                'success_list_url'	=> $this->getListSuccessUrl($insert_result)
            ));
        }
        $this->set_echo_and_die();
    }

    protected function validation_layout($validation_result)
    {
        @ob_end_clean();
        echo json_encode($validation_result);
        $this->set_echo_and_die();
    }

    public function set_css($css_file)
    {
        $this->css_files[sha1($css_file)] = base_url(). '/' . $css_file;
    }

    public function set_js($js_file)
    {
        $this->js_files[sha1($js_file)] = base_url(). '/' .$js_file;
    }

    public function set_js_lib($js_file)
    {
        $this->js_lib_files[sha1($js_file)] = base_url(). '/' .$js_file;
        $this->js_files[sha1($js_file)] = base_url(). '/' .$js_file;
    }

    public function set_js_config($js_file)
    {
        $this->js_config_files[sha1($js_file)] = base_url(). '/' .$js_file;
        $this->js_files[sha1($js_file)] = base_url(). '/' .$js_file;
    }

    public function is_IE7()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])
        && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') !== false)
            ? true : false;
    }

    public function get_css_files()
    {
        return $this->css_files;
    }

    public function get_js_files()
    {
        return $this->js_files;
    }

    public function get_js_lib_files()
    {
        return $this->js_lib_files;
    }

    public function get_js_config_files()
    {
        return $this->js_config_files;
    }

    protected function load_js_chosen()
    {
        $this->set_css($this->default_css_path.'/jquery_plugins/chosen/chosen.css');
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.chosen.min.js');
    }

    protected function load_js_jqueryui()
    {
        $this->set_css($this->default_css_path.'/ui/simple/' . GroceryCrud::JQUERY_UI_CSS);
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/ui/' . GroceryCrud::JQUERY_UI_JS);
    }

    protected function get_layout()
    {
        $js_files = $this->get_js_files();
        $css_files =  $this->get_css_files();

        $js_lib_files = $this->get_js_lib_files();
        $js_config_files = $this->get_js_config_files();

        if ($this->unset_jquery) {
            unset($js_files[sha1($this->default_javascript_path.'/'.GroceryCrud::JQUERY)]);
        }

        if ($this->unset_jquery_ui) {
            unset($css_files[sha1($this->default_css_path.'/ui/simple/'.GroceryCrud::JQUERY_UI_CSS)]);
            unset($js_files[sha1($this->default_javascript_path.'/jquery_plugins/ui/'.GroceryCrud::JQUERY_UI_JS)]);
        }

        if ($this->unset_bootstrap) {
            unset($js_files[sha1($this->default_theme_path.'/bootstrap/js/bootstrap/dropdown.js')]);
            unset($js_files[sha1($this->default_theme_path.'/bootstrap/js/bootstrap/modal.js')]);
            unset($js_files[sha1($this->default_theme_path.'/bootstrap/js/bootstrap/dropdown.min.js')]);
            unset($js_files[sha1($this->default_theme_path.'/bootstrap/js/bootstrap/modal.min.js')]);
            unset($css_files[sha1($this->default_theme_path.'/bootstrap/css/bootstrap/bootstrap.css')]);
            unset($css_files[sha1($this->default_theme_path.'/bootstrap/css/bootstrap/bootstrap.min.css')]);
            unset($css_files[sha1($this->default_theme_path.'/bootstrap-v4/css/bootstrap/bootstrap.css')]);
            unset($css_files[sha1($this->default_theme_path.'/bootstrap-v4/css/bootstrap/bootstrap.min.css')]);
        }

        if($this->echo_and_die === false)
        {
            /** Initialize JavaScript variables */
            $js_vars =  array(
                'default_javascript_path'	=> base_url() . '/' . $this->default_javascript_path,
                'default_css_path'			=> base_url() . '/' . $this->default_css_path,
                'default_texteditor_path'	=> base_url() . '/' . $this->default_texteditor_path,
                'default_theme_path'		=> base_url() . '/' . $this->default_theme_path,
                'base_url'				 	=> base_url() . '/'
            );
            $this->_add_js_vars($js_vars);

            return (object)array(
                'js_files' => $js_files,
                'js_lib_files' => $js_lib_files,
                'js_config_files' => $js_config_files,
                'css_files' => $css_files,
                'output' => $this->views_as_string,
            );
        }
        elseif($this->echo_and_die === true)
        {
            echo $this->views_as_string;
            die();
        }
    }

    protected function update_layout($update_result = false, $state_info = null)
    {
        @ob_end_clean();
        if($update_result === false)
        {
            echo json_encode(array('success' => $update_result));
        }
        else
        {
            $success_message = '<p>'.$this->l('update_success_message');
            if(!$this->unset_back_to_list && !$this->_is_ajax())
            {
                $success_message .= " <a href='".$this->getListUrl()."'>".$this->l('form_go_back_to_list')."</a>";
            }
            $success_message .= '</p>';

            echo json_encode(array(
                'success' => true ,
                'insert_primary_key' => $update_result,
                'success_message' => $success_message,
                'success_list_url'	=> $this->getListSuccessUrl($state_info->primary_key)
            ));
        }
        $this->set_echo_and_die();
    }

    protected function get_integer_input($field_info,$value)
    {
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.numeric.min.js');
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.numeric.config.js');
        $extra_attributes = '';
        if(!empty($field_info->db_max_length))
            $extra_attributes .= "maxlength='{$field_info->db_max_length}'";
        $input = "<input id='field-{$field_info->name}' name='{$field_info->name}' type='text' value='$value' class='numeric form-control' $extra_attributes />";
        return $input;
    }

    protected function get_true_false_input($field_info,$value)
    {
        $value_is_null = empty($value) && $value !== '0' && $value !== 0 ? true : false;

        $input = "<div class='pretty-radio-buttons'>";

        $true_string = is_array($field_info->extras) && array_key_exists(1,$field_info->extras) ? $field_info->extras[1] : $this->default_true_false_text[1];
        $checked = $value === '1' || ($value_is_null && $field_info->default === '1') ? "checked = 'checked'" : "";
        $input .=
            "<div class=\"radio\"><label>
				<input id='field-{$field_info->name}-true' type=\"radio\" name=\"{$field_info->name}\" value=\"1\" $checked />
				$true_string
			 </label> </div>";

        $false_string =  is_array($field_info->extras) && array_key_exists(0,$field_info->extras) ? $field_info->extras[0] : $this->default_true_false_text[0];
        $checked = $value === '0' || ($value_is_null && $field_info->default === '0') ? "checked = 'checked'" : "";
        $input .=
            "<div class=\"radio\"><label>
				<input id='field-{$field_info->name}-false' type=\"radio\" name=\"{$field_info->name}\" value=\"0\" $checked />
				$false_string
			 </label> </div>";

        $input .= "</div>";

        return $input;
    }

    protected function get_string_input($field_info,$value)
    {
        $value = !is_string($value) ? '' : str_replace('"',"&quot;",$value);

        $extra_attributes = '';
        if (!empty($field_info->db_max_length)) {

            if (in_array($field_info->type, array("decimal", "float"))) {
                $decimal_lentgh = explode(",", $field_info->db_max_length);
                $decimal_lentgh = ((int)$decimal_lentgh[0]) + 1;

                $extra_attributes .= "maxlength='" . $decimal_lentgh . "'";
            } else {
                $extra_attributes .= "maxlength='{$field_info->db_max_length}'";
            }

        }
        $input = "<input id='field-{$field_info->name}' class='form-control' name='{$field_info->name}' type='text' value=\"$value\" $extra_attributes />";
        return $input;
    }

    protected function get_text_input($field_info,$value)
    {
        if($field_info->extras == 'text_editor')
        {
            $editor = $this->config->default_text_editor;
            switch ($editor) {
                case 'ckeditor':
                    $this->set_js_lib($this->default_texteditor_path.'/ckeditor/ckeditor.js');
                    $this->set_js_lib($this->default_texteditor_path.'/ckeditor/adapters/jquery.js');
                    $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.ckeditor.config.js');
                    break;

                case 'tinymce':
                    $this->set_js_lib($this->default_texteditor_path.'/tiny_mce/jquery.tinymce.js');
                    $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.tine_mce.config.js');
                    break;

                case 'markitup':
                    $this->set_css($this->default_texteditor_path.'/markitup/skins/markitup/style.css');
                    $this->set_css($this->default_texteditor_path.'/markitup/sets/default/style.css');

                    $this->set_js_lib($this->default_texteditor_path.'/markitup/jquery.markitup.js');
                    $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.markitup.config.js');
                    break;
            }

            $class_name = $this->config->text_editor_type == 'minimal' ? 'mini-texteditor' : 'texteditor';

            $input = "<textarea id='field-{$field_info->name}' name='{$field_info->name}' class='$class_name' >$value</textarea>";
        }
        else
        {
            $input = "<textarea id='field-{$field_info->name}' name='{$field_info->name}' class='form-control'>$value</textarea>";
        }
        return $input;
    }

    protected function get_datetime_input($field_info,$value)
    {
        $this->set_css($this->default_css_path.'/ui/simple/'.GroceryCrud::JQUERY_UI_CSS);
        $this->set_css($this->default_css_path.'/jquery_plugins/jquery.ui.datetime.css');
        $this->set_css($this->default_css_path.'/jquery_plugins/jquery-ui-timepicker-addon.css');
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/ui/'.GroceryCrud::JQUERY_UI_JS);
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery-ui-timepicker-addon.js');

        if($this->language !== 'English')
        {
            include($this->default_config_path.'/language_alias.php');
            if(array_key_exists($this->language, $language_alias))
            {
                $i18n_date_js_file = $this->default_javascript_path.'/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-'.$language_alias[$this->language].'.js';
                if(file_exists($i18n_date_js_file))
                {
                    $this->set_js_lib($i18n_date_js_file);
                }

                $i18n_datetime_js_file = $this->default_javascript_path.'/jquery_plugins/ui/i18n/timepicker/jquery-ui-timepicker-'.$language_alias[$this->language].'.js';
                if(file_exists($i18n_datetime_js_file))
                {
                    $this->set_js_lib($i18n_datetime_js_file);
                }
            }
        }

        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery-ui-timepicker-addon.config.js');

        if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '1970-01-01 00:00:00'){
            list($year,$month,$day) = explode('-',substr($value,0,10));
            $date = date($this->php_date_format, mktime(0,0,0,$month,$day,$year));
            $datetime = $date.substr($value,10);
        }
        else
        {
            $datetime = '';
        }
        $input = "<input id='field-{$field_info->name}' name='{$field_info->name}' type='text' value='$datetime' maxlength='19' class='datetime-input form-control' />
		<a class='datetime-input-clear' tabindex='-1'>".$this->l('form_button_clear')."</a>
		({$this->ui_date_format}) hh:mm:ss";
        return $input;
    }

    protected function get_hidden_input($field_info,$value)
    {
        if($field_info->extras !== null && $field_info->extras != false)
            $value = $field_info->extras;
        $input = "<input id='field-{$field_info->name}' type='hidden' name='{$field_info->name}' value='$value' />";
        return $input;
    }

    protected function get_password_input($field_info,$value)
    {
        $value = !is_string($value) ? '' : $value;

        $extra_attributes = '';
        if(!empty($field_info->db_max_length))
            $extra_attributes .= "maxlength='{$field_info->db_max_length}'";
        $input = "<input id='field-{$field_info->name}' class='form-control' name='{$field_info->name}' type='password' value='$value' $extra_attributes />";
        return $input;
    }

    protected function get_date_input($field_info,$value)
    {
        $this->set_css($this->default_css_path.'/ui/simple/'.GroceryCrud::JQUERY_UI_CSS);
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/ui/'.GroceryCrud::JQUERY_UI_JS);

        if($this->language !== 'English')
        {
            include($this->default_config_path.'/language_alias.php');
            if(array_key_exists($this->language, $language_alias))
            {
                $i18n_date_js_file = $this->default_javascript_path.'/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-'.$language_alias[$this->language].'.js';
                if(file_exists($i18n_date_js_file))
                {
                    $this->set_js_lib($i18n_date_js_file);
                }
            }
        }

        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.datepicker.config.js');

        if(!empty($value) && $value != '0000-00-00' && $value != '1970-01-01')
        {
            list($year,$month,$day) = explode('-',substr($value,0,10));
            $date = date($this->php_date_format, mktime(0,0,0,$month,$day,$year));
        }
        else
        {
            $date = '';
        }

        $input = "<input id='field-{$field_info->name}' name='{$field_info->name}' type='text' value='$date' maxlength='10' class='datepicker-input form-control' />
		<a class='datepicker-input-clear' tabindex='-1'>".$this->l('form_button_clear')."</a> (".$this->ui_date_format.")";
        return $input;
    }

    protected function get_dropdown_input($field_info,$value)
    {
        $this->load_js_chosen();
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $select_title = str_replace('{field_display_as}',$field_info->display_as,$this->l('set_relation_title'));

        $input = "<select id='field-{$field_info->name}' name='{$field_info->name}' class='chosen-select' data-placeholder='".$select_title."'>";
        $options = array('' => '') + $field_info->extras;
        foreach($options as $option_value => $option_label)
        {
            $selected = !empty($value) && $value == $option_value ? "selected='selected'" : '';
            $input .= "<option value='$option_value' $selected >$option_label</option>";
        }

        $input .= "</select>";
        return $input;
    }

    protected function get_enum_input($field_info,$value)
    {
        $this->load_js_chosen();
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $select_title = str_replace('{field_display_as}',$field_info->display_as,$this->l('set_relation_title'));

        $input = "<select id='field-{$field_info->name}' name='{$field_info->name}' class='chosen-select' data-placeholder='".$select_title."'>";
        $options_array = $field_info->extras !== false && is_array($field_info->extras)? $field_info->extras : explode("','",substr($field_info->db_max_length,1,-1));
        $options_array = array('' => '') + $options_array;

        foreach($options_array as $option)
        {
            $selected = !empty($value) && $value == $option ? "selected='selected'" : '';
            $input .= "<option value='$option' $selected >$option</option>";
        }

        $input .= "</select>";
        return $input;
    }

    protected function get_readonly_input($field_info, $value)
    {
        $read_only_value = "&nbsp;";

        if (!empty($value) && !is_array($value)) {
            $read_only_value = $value;
        } elseif (is_array($value)) {
            $all_values = array_values($value);
            $read_only_value = implode(", ",$all_values);
        }

        return '<div id="field-'.$field_info->name.'" class="readonly_label">'.$read_only_value.'</div>';
    }

    protected function get_set_input($field_info,$value)
    {
        $this->load_js_chosen();
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $options_array = $field_info->extras !== false && is_array($field_info->extras)? $field_info->extras : explode("','",substr($field_info->db_max_length,1,-1));
        $selected_values 	= !empty($value) ? explode(",",$value) : array();

        $select_title = str_replace('{field_display_as}',$field_info->display_as,$this->l('set_relation_title'));
        $input = "<select id='field-{$field_info->name}' name='{$field_info->name}[]' multiple='multiple' size='8' class='chosen-multiple-select' data-placeholder='$select_title' style='width:510px;' >";

        foreach($options_array as $option)
        {
            $selected = !empty($value) && in_array($option,$selected_values) ? "selected='selected'" : '';
            $input .= "<option value='$option' $selected >$option</option>";
        }

        $input .= "</select>";

        return $input;
    }

    protected function get_multiselect_input($field_info,$value)
    {
        $this->load_js_chosen();
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $options_array = $field_info->extras;
        $selected_values 	= !empty($value) ? explode(",",$value) : array();

        $select_title = str_replace('{field_display_as}',$field_info->display_as,$this->l('set_relation_title'));
        $input = "<select id='field-{$field_info->name}' name='{$field_info->name}[]' multiple='multiple' size='8' class='chosen-multiple-select' data-placeholder='$select_title' style='width:510px;' >";

        foreach($options_array as $option_value => $option_label)
        {
            $selected = !empty($value) && in_array($option_value,$selected_values) ? "selected='selected'" : '';
            $input .= "<option value='$option_value' $selected >$option_label</option>";
        }

        $input .= "</select>";

        return $input;
    }

    protected function get_relation_input($field_info,$value)
    {
        $this->load_js_chosen();
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $ajax_limitation = 10000;
        $total_rows = $this->get_relation_total_rows($field_info->extras);


        //Check if we will use ajax for our queries or just clien-side javascript
        $using_ajax = $total_rows > $ajax_limitation ? true : false;

        //We will not use it for now. It is not ready yet. Probably we will have this functionality at version 1.4
        $using_ajax = false;

        //If total rows are more than the limitation, use the ajax plugin
        $ajax_or_not_class = $using_ajax ? 'chosen-select' : 'chosen-select';

        $this->_inline_js("var ajax_relation_url = '".$this->getAjaxRelationUrl()."';\n");

        $select_title = str_replace('{field_display_as}',$field_info->display_as,$this->l('set_relation_title'));
        $input = "<select id='field-{$field_info->name}'  name='{$field_info->name}' class='$ajax_or_not_class' data-placeholder='$select_title' style='width:300px'>";
        $input .= "<option value=''></option>";

        if(!$using_ajax)
        {
            $options_array = $this->get_relation_array($field_info->extras);
            foreach($options_array as $option_value => $option)
            {
                $selected = !empty($value) && $value == $option_value ? "selected='selected'" : '';
                $input .= "<option value='$option_value' $selected >$option</option>";
            }
        }
        elseif(!empty($value) || (is_numeric($value) && $value == '0') ) //If it's ajax then we only need the selected items and not all the items
        {
            $selected_options_array = $this->get_relation_array($field_info->extras, $value);
            foreach($selected_options_array as $option_value => $option)
            {
                $input .= "<option value='$option_value'selected='selected' >$option</option>";
            }
        }

        $input .= "</select>";
        return $input;
    }

    protected function get_relation_readonly_input($field_info,$value)
    {
        $options_array = $this->get_relation_array($field_info->extras);

        $value = isset($options_array[$value]) ? $options_array[$value] : '';

        return $this->get_readonly_input($field_info, $value);
    }

    protected function get_upload_file_readonly_input($field_info,$value)
    {
        $file = $file_url = base_url().$field_info->extras->upload_path.'/'.$value;

        $value = !empty($value) ? '<a href="'.$file.'" target="_blank">'.$value.'</a>' : '';

        return $this->get_readonly_input($field_info, $value);
    }

    protected function get_relation_n_n_input($field_info_type, $selected_values)
    {
        $this->set_css($this->default_css_path.'/jquery_plugins/chosen/chosen.css');
        $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.chosen.min.js');
        $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');

        $this->_inline_js("var ajax_relation_url = '".$this->getAjaxRelationUrl()."';\n");

        $field_info 		= $this->relation_n_n[$field_info_type->name]; //As we use this function the relation_n_n exists, so don't need to check
        $unselected_values 	= $this->get_relation_n_n_unselected_array($field_info, $selected_values);

        if(empty($unselected_values) && empty($selected_values))
        {
            $input = "Please add {$field_info_type->display_as} first";
        }
        else
        {
            $select_title = str_replace('{field_display_as}',$field_info_type->display_as,$this->l('set_relation_title'));
            $input = "<select id='field-{$field_info_type->name}' name='{$field_info_type->name}[]' multiple='multiple' size='8' class='chosen-multiple-select' data-placeholder='$select_title' style='min-width:450px; width:100%' >";

            if(!empty($unselected_values))
                foreach($unselected_values as $id => $name)
                {
                    $input .= "<option value='$id'>$name</option>";
                }

            if(!empty($selected_values))
                foreach($selected_values as $id => $name)
                {
                    $input .= "<option value='$id' selected='selected'>$name</option>";
                }

            $input .= "</select>";
        }

        return $input;
    }

    protected function _convert_bytes_ui_to_bytes($bytes_ui)
    {
        $bytes_ui = str_replace(' ','',$bytes_ui);
        if(strstr($bytes_ui,'MB'))
            $bytes = (int)(str_replace('MB','',$bytes_ui))*1024*1024;
        elseif(strstr($bytes_ui,'KB'))
            $bytes = (int)(str_replace('KB','',$bytes_ui))*1024;
        elseif(strstr($bytes_ui,'B'))
            $bytes = (int)(str_replace('B','',$bytes_ui));
        else
            $bytes = (int)($bytes_ui);

        return $bytes;
    }

    protected function get_add_hidden_fields()
    {
        return $this->add_hidden_fields;
    }

    protected function get_edit_hidden_fields()
    {
        return $this->edit_hidden_fields;
    }

    protected function get_add_input_fields($field_values = null)
    {
        $fields = $this->get_add_fields();
        $types 	= $this->get_field_types();

        $input_fields = array();

        foreach($fields as $field_num => $field)
        {
            $field_info = $types[$field->field_name];

            $field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;

            if(!isset($this->callback_add_field[$field->field_name]))
            {
                $field_input = $this->get_field_input($field_info, $field_value);
            }
            else
            {
                $field_input = $field_info;
                $field_input->input = call_user_func($this->callback_add_field[$field->field_name], $field_value, null, $field_info);
            }

            switch ($field_info->crud_type) {
                case 'invisible':
                    unset($this->add_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                case 'hidden':
                    $this->add_hidden_fields[] = $field_input;
                    unset($this->add_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                default:
                    $input_fields[$field->field_name] = $field_input;
                    break;
            }


        }

        return $input_fields;
    }

    protected function get_edit_input_fields($field_values = null)
    {
        $fields = $this->get_edit_fields();
        $types 	= $this->get_field_types();

        $input_fields = array();

        foreach($fields as $field_num => $field)
        {
            $field_info = $types[$field->field_name];

            $field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
            if(!isset($this->callback_edit_field[$field->field_name]))
            {
                $field_input = $this->get_field_input($field_info, $field_value);
            }
            else
            {
                $primary_key = $this->getStateInfo()->primary_key;
                $field_input = $field_info;
                $field_input->input = call_user_func($this->callback_edit_field[$field->field_name], $field_value, $primary_key, $field_info, $field_values);
            }

            switch ($field_info->crud_type) {
                case 'invisible':
                    unset($this->edit_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                case 'hidden':
                    $this->edit_hidden_fields[] = $field_input;
                    unset($this->edit_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                default:
                    $input_fields[$field->field_name] = $field_input;
                    break;
            }


        }

        return $input_fields;
    }

    protected function get_clone_input_fields($field_values = null)
    {
        $fields = $this->get_clone_fields();
        $types 	= $this->get_field_types();

        $input_fields = array();

        foreach($fields as $field_num => $field)
        {
            $field_info = $types[$field->field_name];

            $field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
            if(!isset($this->callback_clone_field[$field->field_name]))
            {
                $field_input = $this->get_field_input($field_info, $field_value);
            }
            else
            {
                $primary_key = $this->getStateInfo()->primary_key;
                $field_input = $field_info;
                $field_input->input = call_user_func($this->callback_clone_field[$field->field_name], $field_value, $primary_key, $field_info, $field_values);
            }

            switch ($field_info->crud_type) {
                case 'invisible':
                    unset($this->clone_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                case 'hidden':
                    $this->edit_hidden_fields[] = $field_input;
                    unset($this->clone_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                default:
                    $input_fields[$field->field_name] = $field_input;
                    break;
            }


        }

        return $input_fields;
    }


    protected function get_read_input_fields($field_values = null)
    {
        $read_fields = $this->get_read_fields();

        $this->field_types = null;
        $this->required_fields = null;

        $read_inputs = [];
        foreach ($read_fields as $field) {
            if (!empty($this->change_field_type)
                && isset($this->change_field_type[$field->field_name])
                && $this->change_field_type[$field->field_name]->type == 'hidden') {
                continue;
            }
            $this->fieldType($field->field_name, 'readonly');
        }

        $fields = $this->get_read_fields();
        $types 	= $this->get_field_types();

        $input_fields = array();

        foreach($fields as $field_num => $field)
        {
            $field_info = $types[$field->field_name];

            $field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
            if(!isset($this->callback_read_field[$field->field_name]))
            {
                $field_input = $this->get_field_input($field_info, $field_value);
            }
            else
            {
                $primary_key = $this->getStateInfo()->primary_key;
                $field_input = $field_info;
                $field_input->input = call_user_func($this->callback_read_field[$field->field_name], $field_value, $primary_key, $field_info, $field_values);
            }

            switch ($field_info->crud_type) {
                case 'invisible':
                    unset($this->read_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                case 'hidden':
                    $this->read_hidden_fields[] = $field_input;
                    unset($this->read_fields[$field_num]);
                    unset($fields[$field_num]);
                    break;
                default:
                    $input_fields[$field->field_name] = $field_input;
                    break;

            }

        }

        return $input_fields;
    }

    protected function setThemeBasics()
    {
        $this->theme_path = $this->default_theme_path;
        if(substr($this->theme_path,-1) != '/')
            $this->theme_path = $this->theme_path.'/';

        include($this->theme_path.$this->theme.'/config.php');

        $this->theme_config = $config;
    }

    /**
     * The setTheme is used in order to change the default theme.
     *
     * @param string $theme
     * @return $this
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    protected function _get_ajax_results()
    {
        //This is a $_POST request rather that $_GET request , because
        //Codeigniter doesn't like the $_GET requests so much!
        if ($this->_is_ajax()) {
            @ob_end_clean();
            $results= (object)array(
                'output' => $this->views_as_string,
                'js_files' => array_values($this->get_js_files()),
                'js_lib_files' => array_values($this->get_js_lib_files()),
                'js_config_files' => array_values($this->get_js_config_files()),
                'css_files' => array_values($this->get_css_files())
            );

            echo json_encode($results);
            die;
        }
        //else just continue
    }

    protected function _is_ajax()
    {
        return array_key_exists('is_ajax', $_POST) && $_POST['is_ajax'] == 'true' ? true: false;
    }

    protected function _theme_view($view, $vars = array(), $return = FALSE)
    {
        $vars = (is_object($vars)) ? get_object_vars($vars) : $vars;

        $file_exists = FALSE;

        $ext = pathinfo($view, PATHINFO_EXTENSION);
        $file = ($ext == '') ? $view.'.php' : $view;

        $view_file = $this->theme_path.$this->theme.'/views/';

        if (file_exists($view_file.$file))
        {
            $path = $view_file.$file;
            $file_exists = TRUE;
        }

        if ( ! $file_exists)
        {
            throw new Exception('Unable to load the requested file: '.$file, 16);
        }

        extract($vars);

        #region buffering...
        ob_start();

        include($path);

        $buffer = ob_get_contents();
        @ob_end_clean();
        #endregion

        if ($return === TRUE)
        {
            return $buffer;
        }

        $this->views_as_string .= $buffer;
    }

    protected function _inline_js($inline_js = '')
    {
        $this->views_as_string .= "<script type=\"text/javascript\">\n{$inline_js}\n</script>\n";
    }

    protected function _add_js_vars($js_vars = array())
    {
        $javascript_as_string = "<script type=\"text/javascript\">\n";
        foreach ($js_vars as $js_var => $js_value) {
            $javascript_as_string .= "\tvar $js_var = '$js_value';\n";
        }
        $javascript_as_string .= "\n</script>\n";
        $this->views_as_string .= $javascript_as_string;
    }

    protected function get_views_as_string()
    {
        if(!empty($this->views_as_string))
            return $this->views_as_string;
        else
            return null;
    }
}


/**
 * PHP grocery CRUD
 *
 * LICENSE
 *
 * Grocery CRUD is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	grocery CRUD
 * @copyright  	Copyright (c) 2010 through 2014, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

// ------------------------------------------------------------------------

/**
 * PHP grocery States
 *
 * States of grocery CRUD
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @version    	2.0.0
 */
class grocery_CRUD_States extends grocery_CRUD_Layout
{
    const STATE_UNKNOWN = 0;
    const STATE_LIST = 1;
    const STATE_ADD = 2;
    const STATE_EDIT = 3;
    const STATE_DELETE = 4;
    const STATE_INSERT = 5;
    const STATE_READ = 18;
    const STATE_DELETE_MULTIPLE = 19;
    const STATE_CLONE = 20;

    protected $states = array(
        0	=> 'unknown',
        1	=> 'list',
        2	=> 'add',
        3	=> 'edit',
        4	=> 'delete',
        5	=> 'insert',
        6	=> 'update',
        7	=> 'ajax_list',
        8   => 'ajax_list_info',
        9	=> 'insert_validation',
        10	=> 'update_validation',
        15	=> 'success',
        16  => 'export',
        17  => 'print',
        18  => 'read',
        19  => 'delete_multiple',
        20  => 'clone'
    );

    /**
     * Get all the information about the current state.
     *
     * @return object
     * @throws Exception
     */
    public function getStateInfo()
    {
        $state_code = $this->getStateCode();
        $segment_object = $this->get_state_info_from_url();

        $first_parameter = $segment_object->first_parameter;
        $second_parameter = $segment_object->second_parameter;

        $state_info = (object)array();

        switch ($state_code) {
            case self::STATE_LIST:
            case self::STATE_ADD:
                //for now... do nothing! Keeping this switch here in case we need any information at the future.
                break;

            case self::STATE_EDIT:
            case self::STATE_READ:
                if ($first_parameter !== null) {
                    $state_info = (object) array('primary_key' => $first_parameter);
                } else {
                    throw new Exception('On the state "edit" the Primary key cannot be null', 6);
                    die();
                }
                break;

            case self::STATE_DELETE:
                if ($first_parameter !== null) {
                    $state_info = (object) array('primary_key' => $first_parameter);
                } else {
                    throw new Exception('On the state "delete" the Primary key cannot be null',7);
                    die();
                }
                break;

            case self::STATE_DELETE_MULTIPLE:
                if (!empty($_POST) && !empty($_POST['ids']) && is_array($_POST['ids'])) {
                    $state_info = (object) array('ids' => $_POST['ids']);
                } else {
                    throw new Exception('On the state "Delete Multiple" you need send the ids as a post array.');
                    die();
                }
                break;

            case self::STATE_CLONE:
                if ($first_parameter !== null) {
                    $state_info = (object) array('primary_key' => $first_parameter);
                } else {
                    throw new Exception('On the state "clone" the Primary key cannot be null', 20);
                    die();
                }
                break;

            case self::STATE_INSERT:
                if(!empty($_POST))
                {
                    $state_info = (object)array('unwrapped_data' => $_POST);
                }
                else
                {
                    throw new Exception('On the state "insert" you must have post data',8);
                    die();
                }
                break;

            case 6:
                if(!empty($_POST) && $first_parameter !== null)
                {
                    $state_info = (object)array('primary_key' => $first_parameter,'unwrapped_data' => $_POST);
                }
                elseif(empty($_POST))
                {
                    throw new Exception('On the state "update" you must have post data',9);
                    die();
                }
                else
                {
                    throw new Exception('On the state "update" the Primary key cannot be null',10);
                    die();
                }
                break;

            case 7:
            case 8:
            case 16: //export to excel
            case 17: //print
                $state_info = (object)array();
                $data = !empty($_POST) ? $_POST : $_GET;

                if(!empty($data['per_page']))
                {
                    $state_info->per_page = is_numeric($data['per_page']) ? $data['per_page'] : null;
                }
                if(!empty($data['page']))
                {
                    $state_info->page = is_numeric($data['page']) ? $data['page'] : null;
                }
                //If we request an export or a print we don't care about what page we are
                if($state_code === 16 || $state_code === 17)
                {
                    $state_info->page = 1;
                    $state_info->per_page = 1000000; //a very big number!
                }
                if(!empty($data['order_by'][0]))
                {
                    $state_info->order_by = $data['order_by'];
                }
                if(isset($data['search_text']) && $data['search_text'] !== '')
                {
                    if(empty($data['search_field']))
                    {
                        $search_text = strip_tags($data['search_field']);
                        $state_info->search = (object)array('field' => null , 'text' => $data['search_text']);
                    }
                    else
                    {
                        if (is_array($data['search_field'])) {
                            $search_array = array();
                            foreach ($data['search_field'] as $search_key => $search_field_name) {
                                $search_field_name = preg_replace("/[=\"'?\\\\]/", '' , $search_field_name);
                                $search_array[$search_field_name] = isset($data['search_text'][$search_key]) ? $data['search_text'][$search_key] : '';
                            }
                            $state_info->search	= $search_array;
                        } else {
                            $field_name = preg_replace("/[=\"'?\\\\]/", '' , $data['search_field']);
                            $state_info->search	= (object)array(
                                'field' => $field_name,
                                'text' => $data['search_text'] );
                        }
                    }
                }
                break;

            case 9:

                break;

            case 10:
                if($first_parameter !== null)
                {
                    $state_info = (object)array('primary_key' => $first_parameter);
                }
                break;

            case 11:
                $state_info->field_name = $first_parameter;
                break;

            case 12:
                $state_info->field_name = $first_parameter;
                $state_info->file_name = $second_parameter;
                break;

            case 13:
                $state_info->field_name = $_POST['field_name'];
                $state_info->search 	= $_POST['term'];
                break;

            case 14:
                $state_info->field_name = $_POST['field_name'];
                $state_info->search 	= $_POST['term'];
                break;

            case 15:
                $state_info = (object)array(
                    'primary_key' 		=> $first_parameter,
                    'success_message'	=> true
                );
                break;
        }

        return $state_info;
    }

    protected function getStateCode()
    {
        $state_string = $this->get_state_info_from_url()->operation;

        if( $state_string != 'unknown' && in_array( $state_string, $this->states ) )
            $state_code =  array_search($state_string, $this->states);
        else
            $state_code = 0;

        return $state_code;
    }

    protected function state_url($url = '', $is_list_page = false)
    {
        //Easy scenario, we had set the crud_url_path
        if (!empty($this->crud_url_path)) {
            $state_url = !empty($this->list_url_path) && $is_list_page?
                $this->list_url_path :
                $this->crud_url_path.'/'.$url ;
        } else {
            //Complicated scenario. The crud_url_path is not specified so we are
            //trying to understand what is going on from the URL.
            $segment_object = $this->get_state_info_from_url();
            $segment_position = $segment_object->segment_position;

            $state_url_array = array();
            $segments = explode('/', uri_string());

            if( sizeof($segments) > 0 ) {
                foreach($segments as $num => $value) {
                    $state_url_array[$num] = $value;
                    if($num == ($segment_position - 1)) {
                        break;
                    }
                }
            }

            $operation = $url !== '' ? '/' . $url : '';

            $state_url =  site_url(implode('/',$state_url_array) . $operation);
        }

        return $state_url;
    }

    protected function get_state_info_from_url()
    {
        $segments = explode('/', uri_string());

        $segment_position = count($segments) + 1;
        $operation = 'list';

        foreach($segments as $num => $value) {
            if($value != 'unknown' && in_array($value, $this->states)) {
                // We want to ensure that is the LAST segment with name that is in the array.
                // That's why we are not stopping the foreach statement
                $segment_position = (int)$num;
                $operation = $value;
            }
        }

        $first_parameter = isset($segments[$segment_position+1]) ? $segments[$segment_position+1] : null;
        $second_parameter = isset($segments[$segment_position+2]) ? $segments[$segment_position+2] : null;

        return (object)[
            'segment_position' => $segment_position,
            'operation' => $operation,
            'first_parameter' => $first_parameter,
            'second_parameter' => $second_parameter
        ];
    }

    protected function getUriSegment($position) {
        $segments = explode('/', uri_string());

        if (array_key_exists($position, $segments)) {
            return $segments[$position];
        }

        return false;
    }

    protected function get_method_hash()
    {
        return $this->crud_url_path !== null
            ? md5($this->crud_url_path)
            : md5(uri_string());
    }

    protected function get_method_name()
    {
        $ci = &get_instance();
        return $ci->router->method;
    }

    protected function get_controller_name()
    {
        $ci = &get_instance();
        return $ci->router->class;
    }

    /**
     * Simply get the current state name as a string.
     *
     * @return string
     */
    public function getState()
    {
        return $this->states[$this->getStateCode()];
    }

    protected function getListUrl()
    {
        return $this->state_url('',true);
    }

    protected function getAjaxListUrl()
    {
        return $this->state_url('ajax_list');
    }

    protected function getExportToExcelUrl()
    {
        return $this->state_url('export');
    }

    protected function getPrintUrl()
    {
        return $this->state_url('print');
    }

    protected function getAjaxListInfoUrl()
    {
        return $this->state_url('ajax_list_info');
    }

    protected function getAddUrl()
    {
        return $this->state_url('add');
    }

    protected function getInsertUrl()
    {
        return $this->state_url('insert');
    }

    protected function getValidationInsertUrl()
    {
        return $this->state_url('insert_validation');
    }

    protected function getValidationUpdateUrl($primary_key = null)
    {
        if($primary_key === null)
            return $this->state_url('update_validation');
        else
            return $this->state_url('update_validation/'.$primary_key);
    }

    protected function getCloneUrl($primary_key = null)
    {
        if ($primary_key === null) {
            return $this->state_url('clone');
        } else {
            return $this->state_url('clone/' . $primary_key);
        }
    }

    protected function getEditUrl($primary_key = null)
    {
        if($primary_key === null)
            return $this->state_url('edit');
        else
            return $this->state_url('edit/'.$primary_key);
    }

    protected function getReadUrl($primary_key = null)
    {
        if($primary_key === null)
            return $this->state_url('read');
        else
            return $this->state_url('read/'.$primary_key);
    }

    protected function getUpdateUrl($state_info)
    {
        return $this->state_url('update/'.$state_info->primary_key);
    }

    protected function getDeleteUrl($state_info = null)
    {
        if (empty($state_info)) {
            return $this->state_url('delete');
        } else {
            return $this->state_url('delete/'.$state_info->primary_key);
        }
    }

    protected function getDeleteMultipleUrl()
    {
        return $this->state_url('delete_multiple');
    }

    protected function getListSuccessUrl($primary_key = null)
    {
        if(empty($primary_key))
            return $this->state_url('success',true);
        else
            return $this->state_url('success/'.$primary_key,true);
    }

    protected function getAjaxRelationUrl()
    {
        return $this->state_url('ajax_relation');
    }

    protected function getAjaxRelationManytoManyUrl()
    {
        return $this->state_url('ajax_relation_n_n');
    }
}


/**
 * PHP grocery CRUD
 *
 * LICENSE
 *
 * Grocery CRUD is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	grocery CRUD
 * @copyright  	Copyright (c) 2010 through 2014, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @version    	2.0.0
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

// ------------------------------------------------------------------------

/**
 * PHP grocery CRUD
 *
 * Creates a full functional CRUD with few lines of code.
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @license     https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @link		http://www.grocerycrud.com/documentation
 */
class GroceryCrud extends grocery_CRUD_States
{
    /**
     * Grocery CRUD version
     *
     * @var	string
     */
    const	VERSION = "2.0.0";

    const	JQUERY 			= "jquery-1.11.1.min.js";
    const	JQUERY_UI_JS 	= "jquery-ui-1.10.3.custom.min.js";
    const	JQUERY_UI_CSS 	= "jquery-ui-1.10.1.custom.min.css";

    const THEME_FLEXIGRID    = 'flexigrid';
    const THEME_DATATABLES   = 'datatables';
    const THEME_BOOTSTRAP_V3 = 'bootstrap';
    const THEME_BOOTSTRAP_V4 = 'bootstrap-v4';

    protected $state_code 			= null;
    protected $state_info 			= null;
    protected $columns				= null;

    private $basic_db_table_checked = false;
    private $columns_checked		= false;
    private $add_fields_checked		= false;
    private $edit_fields_checked	= false;
    private $clone_fields_checked	= false;
    private $read_fields_checked	= false;

    protected $default_theme		= 'flexigrid';
    protected $language				= null;
    protected $lang_strings			= array();
    protected $php_date_format		= null;
    protected $js_date_format		= null;
    protected $ui_date_format		= null;
    protected $character_limiter    = null;
    protected $config    			= null;

    protected $add_fields			= null;
    protected $edit_fields			= null;
    protected $clone_fields			= null;
    protected $read_fields			= null;
    protected $add_hidden_fields 	= array();
    protected $edit_hidden_fields 	= array();
    protected $field_types 			= null;
    protected $basic_db_table 		= null;
    protected $theme_config 		= array();
    protected $subject 				= null;
    protected $subject_plural 		= null;
    protected $display_as 			= array();
    protected $order_by 			= null;
    protected $where 				= array();
    protected $like 				= array();
    protected $having 				= array();
    protected $or_having 			= array();
    protected $limit 				= null;
    protected $required_fields		= array();
    protected $_unique_fields 			= array();
    protected $validation_rules		= array();
    protected $relation				= array();
    protected $relation_n_n			= array();
    protected $actions				= array();

    protected $form_validation		= null;
    protected $change_field_type	= null;
    protected $primary_keys			= array();
    protected $crud_url_path		= null;
    protected $list_url_path		= null;

    /* The setters */
    protected $set_texteditor		= array();

    /* The unsetters */
    protected $unset_add			= false;
    protected $unset_edit			= false;
    protected $unset_delete			= false;
    protected $unset_read			= true;
    protected $unset_jquery			= false;
    protected $unset_jquery_ui		= false;
    protected $unset_bootstrap 		= false;
    protected $unset_list			= false;
    protected $unset_export			= false;
    protected $unset_print			= false;
    protected $unset_back_to_list	= false;
    protected $unset_clone			= true;
    protected $unset_columns		= null;
    protected $unset_add_fields 	= null;
    protected $unset_edit_fields	= null;
    protected $unset_clone_fields	= null;
    protected $unset_read_fields	= null;

    /* Callbacks */
    protected $callback_before_insert 	= null;
    protected $callback_after_insert 	= null;
    protected $callback_insert 			= null;
    protected $callback_before_update 	= null;
    protected $callback_after_update 	= null;
    protected $callback_update 			= null;
    protected $callback_before_delete 	= null;
    protected $callback_after_delete 	= null;
    protected $callback_delete 			= null;
    protected $callback_before_clone 	= null;
    protected $callback_after_clone 	= null;
    protected $callback_clone 			= null;
    protected $callback_column			= array();
    protected $callback_add_field		= array();
    protected $callback_edit_field		= array();
    protected $callback_read_field		= array();
    protected $callback_clone_field		= array();

    protected $default_javascript_path	= null; //autogenerate, please do not modify
    protected $default_css_path			= null; //autogenerate, please do not modify
    protected $default_texteditor_path 	= null; //autogenerate, please do not modify
    protected $default_theme_path		= null; //autogenerate, please do not modify
    protected $default_language_path	= 'assets/grocery_crud/languages';
    protected $default_config_path		= 'assets/grocery_crud/config';
    protected $default_assets_path		= 'assets/grocery_crud';

    /**
     *
     * Constructor
     *
     * @access	public
     */
    public function __construct()
    {

    }

    /**
     * Specifying the fields that the end user will see as the datagrid columns.
     *
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * The setRule function is used to set a validation rule at the backend.
     *
     * @param string $field
     * @param string $label
     * @param string $rules
     * @param array $errors
     * @return $this
     */
    function setRule(string $field, string $label, string $rules, array $errors = []) {
        $this->validation_rules[$field] = [
            'field' => $field,
            'label' => $label,
            'rules' => $rules,
            'errors' => $errors
        ];

        return $this;
    }

    /**
     * Changing the default field type from the database to fit to our needs.
     *
     * @param string $field
     * @param string $type
     * @param array|null $extras
     * @return $this
     */
    public function fieldType($field , $type, $extras = null)
    {
        $this->change_field_type[$field] = (object)[
            'type' => $type,
            'extras' => $extras
        ];

        return $this;
    }

    /**
     * Change the default primary key for a specific table.
     * If the $table_name is NULL then the primary key is for the default table name that we added at the set_table method
     *
     * @param string $primaryKey
     * @param null $tableName
     * @return $this
     */
    public function setPrimaryKey(string $primaryKey, $tableName = null)
    {
        $this->primary_keys[] = array('field_name' => $primaryKey, 'table_name' => $tableName);

        return $this;
    }

    /**
     * Specifying the fields that will open with a texteditor (ckeditor).
     *
     * @param array $fields
     * @return $this
     */
    public function setTexteditor(array $fields) {
        $this->set_texteditor = $fields;

        return $this;
    }

    /**
     * Unsets the texteditor for the selected fields. This function is really rare to use as by default there is not
     * any load of the texteditor for optimising purposes.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetTexteditor(array $fields)
    {
        foreach ($fields as $field) {
            if(in_array($field, $this->set_texteditor)) {
                unset($this->set_texteditor[array_search($field, $this->set_texteditor)]);
            }
        }

        return $this;
    }

    /**
     * 	Do not load jQuery. This is used when jQuery is already loaded at the template.
     *
     * @return $this
     */
    public function unsetJquery()
    {
        $this->unset_jquery = true;

        return $this;
    }

    /**
     * Do not load jQuery UI. This is used when the jQuery UI (CSS and JS) is already loaded at the template.
     *
     * @return $this
     */
    public function unsetJqueryUi()
    {
        $this->unset_jquery_ui = true;

        return $this;
    }

    /**
     * Do not load Bootstrap CSS. This is used when the Bootstrap CSS is already loaded at the template.
     *
     * @return $this
     */
    public function unsetBootstrap()
    {
        $this->unset_bootstrap = true;

        return $this;
    }

    /**
     * Setting the insert functionality. This function is rare to use as the default is already enabled.
     *
     * @return $this
     */
    public function setAdd() {
        $this->unset_add = false;

        return $this;
    }

    /**
     * Setting the delete functionality. This function is rare to use as the default is already enabled.
     *
     * @return $this
     */
    public function setDelete() {
        $this->unset_delete = false;

        return $this;
    }

    /**
     * Setting the update functionality. This function is rare to use as the default is already enabled.
     *
     * @return $this
     */
    public function setEdit() {
        $this->unset_edit = false;

        return $this;
    }

    /**
     * Setting the export functionality. This function is rare to use as the default is already enabled.
     *
     * @return $this
     */
    public function setExport() {
        $this->unset_export = false;

        return $this;
    }

    /**
     * Setting the print functionality. This function is rare to use as the default is already enabled.
     *
     * @return $this
     */
    public function setPrint() {
        $this->unset_print = false;

        return $this;
    }


    /**
     * In order to enable the “View” button at your grid you will need to use the function setRead. The view of the form (read only) is false by default.
     *
     * @return $this
     */
    public function setRead() {
        $this->unset_read = false;

        return $this;
    }

    /**
     * Removing the insert functionality at the current CRUD.
     *
     * @return $this
     */
    public function unsetAdd()
    {
        $this->unset_add = true;
        $this->unset_clone = true;

        return $this;
    }

    /**
     * Removing the edit operation for the end-user (from the frontend and the backend)
     *
     * @return $this
     */
    public function unsetEdit()
    {
        $this->unset_edit = true;

        return $this;
    }

    /**
     * Unset (and do not display) the delete functionality (also unsetting the delete multiple functionality)
     *
     * @return $this
     */
    public function unsetDelete()
    {
        $this->unset_delete = true;

        return $this;
    }

    /**
     * The method unsetRead is removing completely the Read operation for the end-user.
     *
     * @return $this
     */
    public function unsetRead()
    {
        $this->unset_read = true;

        return $this;
    }

    /**
     * Just an alias to unset_read
     *
     * @return	void
     * */
    public function unset_view()
    {
        return unset_read();
    }

    /**
     * Removing the export functionality for the current CRUD.
     *
     * @return $this
     */
    public function unsetExport()
    {
        $this->unset_export = true;

        return $this;
    }

    /**
     * The method unsetPrint is removing completely the Print operation for the end-user.
     *
     * @return $this
     */
    public function unsetPrint()
    {
        $this->unset_print = true;

        return $this;
    }

    /**
     * Removing all the permissions for any operation (expect print and export) for the end-user.
     *
     * @return $this
     */
    public function unsetOperations()
    {
        $this->unset_add 	= true;
        $this->unset_edit 	= true;
        $this->unset_clone = true;
        $this->unset_delete = true;
        $this->unset_read	= true;

        return $this;
    }

    /**
     * Unset (do not display) the specified columns.
     *
     * @param array $columns
     * @return $this
     */
    public function unsetColumns(array $columns)
    {
        $this->unset_columns = $columns;

        return $this;
    }

    /**
     * Unset (do not display) the specified fields for insert, update, clone and view form.
     * This method is simply combining the methods: unsetAddFields, unsetEditFields, unsetCloneFields, unsetReadFields.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetFields(array $fields)
    {
        $this->unset_add_fields = $fields;
        $this->unset_edit_fields = $fields;
        $this->unset_clone_fields = $fields;
        $this->unset_read_fields = $fields;

        return $this;
    }

    /**
     * Unset (do not display) the specified fields for the insert form.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetAddFields(array $fields)
    {
        $this->unset_add_fields = $fields;

        return $this;
    }


    /**
     * Unset (do not display) the specified fields for the update form.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetEditFields(array $fields)
    {
        $this->unset_edit_fields = $fields;

        return $this;
    }

    /**
     * Unset (do not display) the specified fields from the clone form.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetCloneFields(array $fields)
    {
        $this->unset_clone_fields = $fields;

        return $this;
    }


    /**
     * Unset (do not display) the specified fields for the view (read only) form.
     *
     * @param array $fields
     * @return $this
     */
    public function unsetReadFields(array $fields)
    {
        $this->unset_read_fields = $fields;

        return $this;
    }

    /**
     * Unsets everything that has to do with buttons or links with go back to datagrid message
     *
     * @return $this
     */
    public function unsetBackToDatagrid()
    {
        $this->unset_back_to_list = true;

        return $this;
    }

    /**
     * Enabling the clone functionality for the datagrid. Clone is basically copying all the data to an insert form.
     *
     * @return $this
     */
    public function setClone() {
        $this->unset_clone = false;
        $this->unset_add = false;

        return $this;
    }

    /**
     * The method unsetClone is removing completely the Clone operation for the end-user.
     *
     * @return $this
     */
    public function unsetClone()
    {
        $this->unset_clone = true;

        return $this;
    }

    /**
     * This function is really just a facade function to call all the 4 functions at once: addFields, editFields, readFields and cloneFields.
     *
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->add_fields = $fields;
        $this->edit_fields = $fields;
        $this->read_fields = $fields;
        $this->clone_fields = $fields;

        return $this;
    }

    /**
     * The fields that will be visible to the end user for add/insert form.
     *
     * @param array $addFields
     * @return $this
     */
    public function addFields(array $addFields)
    {
        $this->add_fields = $addFields;

        return $this;
    }

    /**
     * The fields that will be visible to the end user for clone form.
     *
     * @param array $cloneFields
     * @return $this
     */
    public function cloneFields(array $cloneFields)
    {
        $this->clone_fields = $cloneFields;

        return $this;
    }

    /**
     * The fields that will be visible to the end user for edit/update form.
     *
     * @param array $editFields
     * @return $this
     */
    public function editFields(array $editFields)
    {
        $this->edit_fields = $editFields;

        return $this;
    }

    /**
     * The fields that will be visible when the end-user navigates to the view form.
     *
     * @param array $readFields
     * @return $this
     */
    public function readFields(array $readFields)
    {
        $this->read_fields = $readFields;

        return $this;
    }

    /**
     * Displaying the field name with a more readable label to the end-user.
     *
     * @param string|array $fieldName
     * @param string|null $displayAs
     * @return $this
     */
    public function displayAs($fieldName, $displayAs = null)
    {
        if(is_array($fieldName))
        {
            foreach($fieldName as $field => $displayAs)
            {
                $this->display_as[$field] = $displayAs;
            }
        }
        elseif($displayAs !== null)
        {
            $this->display_as[$fieldName] = $displayAs;
        }
        return $this;
    }

    /**
     *
     * Load the language strings array from the language file
     */
    protected function _load_language()
    {
        if($this->language === null)
        {
            $this->language = $this->config->default_language;
        }
        include($this->default_language_path.'/'.$this->language.'.php');

        foreach($lang as $handle => $lang_string)
            if(!isset($this->lang_strings[$handle]))
                $this->lang_strings[$handle] = $lang_string;

        $this->default_true_false_text = array( $this->l('form_inactive') , $this->l('form_active'));
        $this->subject = $this->subject === null ? $this->l('list_record') : $this->subject;

    }

    protected function _load_date_format()
    {
        list($php_day, $php_month, $php_year) = array('d','m','Y');
        list($js_day, $js_month, $js_year) = array('dd','mm','yy');
        list($ui_day, $ui_month, $ui_year) = array($this->l('ui_day'), $this->l('ui_month'), $this->l('ui_year'));

        $date_format = $this->config->date_format;
        switch ($date_format) {
            case 'uk-date':
                $this->php_date_format 		= "$php_day/$php_month/$php_year";
                $this->js_date_format		= "$js_day/$js_month/$js_year";
                $this->ui_date_format		= "$ui_day/$ui_month/$ui_year";
                break;

            case 'us-date':
                $this->php_date_format 		= "$php_month/$php_day/$php_year";
                $this->js_date_format		= "$js_month/$js_day/$js_year";
                $this->ui_date_format		= "$ui_month/$ui_day/$ui_year";
                break;

            case 'sql-date':
            default:
                $this->php_date_format 		= "$php_year-$php_month-$php_day";
                $this->js_date_format		= "$js_year-$js_month-$js_day";
                $this->ui_date_format		= "$ui_year-$ui_month-$ui_day";
                break;
        }
    }

    /**
     * Change any handle of the translation.
     *
     * @param string $handle
     * @param string $langString
     * @return $this
     */
    public function setLangString(string $handle, string $langString){
        $this->lang_strings[$handle] = $langString;

        return $this;
    }

    /**
     * Just an alias to get_lang_string method
     *
     * @param $handle
     * @return string
     */
    public function l(string $handle)
    {
        return $this->get_lang_string($handle);
    }

    /**
     * Get the language string of the inserted string handle
     *
     * @param string $handle
     * @return string
     */
    public function get_lang_string(string $handle)
    {
        return $this->lang_strings[$handle];
    }

    /**
     * Set the language of the CRUD.
     *
     * @example setLanguage('Greek')
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     *
     * Enter description here ...
     */
    protected function get_columns()
    {
        if($this->columns_checked === false)
        {
            $field_types = $this->get_field_types();
            if(empty($this->columns))
            {
                $this->columns = array();
                foreach($field_types as $field)
                {
                    if( !isset($field->db_extra) || $field->db_extra != 'auto_increment' )
                        $this->columns[] = $field->name;
                }
            }

            foreach($this->columns as $col_num => $column)
            {

                if(isset($this->relation[$column]))
                {

                    $new_column = $this->_unique_field_name($this->relation[$column][0]);
                    $this->columns[$col_num] = $new_column;

                    if(isset($this->display_as[$column]))
                    {
                        $display_as = $this->display_as[$column];
                        unset($this->display_as[$column]);
                        $this->display_as[$new_column] = $display_as;
                    }
                    else
                    {
                        $this->display_as[$new_column] = ucfirst(str_replace('_',' ',$column));
                    }

                    $column = $new_column;
                    $this->columns[$col_num] = $new_column;
                }
                else
                {
                    if(!empty($this->relation))
                    {
                        $table_name  = $this->get_table();
                        foreach($this->relation as $relation)
                        {
                            if( $relation[2] == $column )
                            {
                                $new_column = $table_name.'.'.$column;
                                if(isset($this->display_as[$column]))
                                {
                                    $display_as = $this->display_as[$column];
                                    unset($this->display_as[$column]);
                                    $this->display_as[$new_column] = $display_as;
                                }
                                else
                                {
                                    $this->display_as[$new_column] = ucfirst(str_replace('_',' ',$column));
                                }

                                $column = $new_column;
                                $this->columns[$col_num] = $new_column;
                            }
                        }
                    }

                }

                if(isset($this->display_as[$column]))
                    $this->columns[$col_num] = (object)array('field_name' => $column, 'display_as' => $this->display_as[$column]);
                elseif(isset($field_types[$column]))
                    $this->columns[$col_num] = (object)array('field_name' => $column, 'display_as' => $field_types[$column]->display_as);
                else
                    $this->columns[$col_num] = (object)array('field_name' => $column, 'display_as' =>
                        ucfirst(str_replace('_',' ',$column)));

                if(!empty($this->unset_columns) && in_array($column,$this->unset_columns))
                {
                    unset($this->columns[$col_num]);
                }
            }

            $this->columns_checked = true;

        }

        return $this->columns;
    }

    /**
     *
     * Enter description here ...
     */
    protected function get_add_fields()
    {
        if($this->add_fields_checked === false)
        {
            $field_types = $this->get_field_types();
            if(!empty($this->add_fields))
            {
                foreach($this->add_fields as $field_num => $field)
                {
                    if(isset($this->display_as[$field]))
                        $this->add_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $this->display_as[$field]);
                    elseif(isset($field_types[$field]->display_as))
                        $this->add_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $field_types[$field]->display_as);
                    else
                        $this->add_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => ucfirst(str_replace('_',' ',$field)));
                }
            }
            else
            {
                $this->add_fields = array();
                foreach($field_types as $field)
                {
                    //Check if an unset_add_field is initialize for this field name
                    if($this->unset_add_fields !== null && is_array($this->unset_add_fields) && in_array($field->name,$this->unset_add_fields))
                        continue;

                    if( (!isset($field->db_extra) || $field->db_extra != 'auto_increment') )
                    {
                        if(isset($this->display_as[$field->name]))
                            $this->add_fields[] = (object)array('field_name' => $field->name, 'display_as' => $this->display_as[$field->name]);
                        else
                            $this->add_fields[] = (object)array('field_name' => $field->name, 'display_as' => $field->display_as);
                    }
                }
            }

            $this->add_fields_checked = true;
        }
        return $this->add_fields;
    }

    /**
     *
     * Enter description here ...
     */
    protected function get_edit_fields()
    {
        if($this->edit_fields_checked === false)
        {
            $field_types = $this->get_field_types();
            if(!empty($this->edit_fields))
            {
                foreach($this->edit_fields as $field_num => $field)
                {
                    if(isset($this->display_as[$field]))
                        $this->edit_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $this->display_as[$field]);
                    else
                        $this->edit_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $field_types[$field]->display_as);
                }
            }
            else
            {
                $this->edit_fields = array();
                foreach($field_types as $field)
                {
                    //Check if an unset_edit_field is initialize for this field name
                    if($this->unset_edit_fields !== null && is_array($this->unset_edit_fields) && in_array($field->name,$this->unset_edit_fields))
                        continue;

                    if(!isset($field->db_extra) || $field->db_extra != 'auto_increment')
                    {
                        if(isset($this->display_as[$field->name]))
                            $this->edit_fields[] = (object)array('field_name' => $field->name, 'display_as' => $this->display_as[$field->name]);
                        else
                            $this->edit_fields[] = (object)array('field_name' => $field->name, 'display_as' => $field->display_as);
                    }
                }
            }

            $this->edit_fields_checked = true;
        }
        return $this->edit_fields;
    }

    /**
     *
     * Enter description here ...
     */
    protected function get_clone_fields()
    {
        if($this->clone_fields_checked === false)
        {
            $field_types = $this->get_field_types();
            if(!empty($this->clone_fields))
            {
                foreach($this->clone_fields as $field_num => $field)
                {
                    if(isset($this->display_as[$field]))
                        $this->clone_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $this->display_as[$field]);
                    else
                        $this->clone_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $field_types[$field]->display_as);
                }
            }
            else
            {
                $this->clone_fields = [];
                foreach($field_types as $field)
                {
                    //Check if an unset_clone_field is initialize for this field name
                    if($this->unset_clone_fields !== null && is_array($this->unset_clone_fields) && in_array($field->name,$this->unset_clone_fields))
                        continue;

                    if(!isset($field->db_extra) || $field->db_extra != 'auto_increment')
                    {
                        if(isset($this->display_as[$field->name]))
                            $this->clone_fields[] = (object)array('field_name' => $field->name, 'display_as' => $this->display_as[$field->name]);
                        else
                            $this->clone_fields[] = (object)array('field_name' => $field->name, 'display_as' => $field->display_as);
                    }
                }
            }

            $this->clone_fields_checked = true;
        }
        return $this->clone_fields;
    }

    /**
     *
     * Enter description here ...
     */
    protected function get_read_fields()
    {
        if($this->read_fields_checked === false)
        {
            $field_types = $this->get_field_types();
            if(!empty($this->read_fields))
            {
                foreach($this->read_fields as $field_num => $field)
                {
                    if(isset($this->display_as[$field]))
                        $this->read_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $this->display_as[$field]);
                    else
                        $this->read_fields[$field_num] = (object)array('field_name' => $field, 'display_as' => $field_types[$field]->display_as);
                }
            }
            else
            {
                $this->read_fields = array();
                foreach($field_types as $field)
                {
                    //Check if an unset_read_field is initialize for this field name
                    if($this->unset_read_fields !== null && is_array($this->unset_read_fields) && in_array($field->name,$this->unset_read_fields))
                        continue;

                    if(!isset($field->db_extra) || $field->db_extra != 'auto_increment')
                    {
                        if(isset($this->display_as[$field->name]))
                            $this->read_fields[] = (object)array('field_name' => $field->name, 'display_as' => $this->display_as[$field->name]);
                        else
                            $this->read_fields[] = (object)array('field_name' => $field->name, 'display_as' => $field->display_as);
                    }
                }
            }

            $this->read_fields_checked = true;
        }
        return $this->read_fields;
    }

    /**
     * The default ordering that the datagrid will have before the user will press any button to order by column.
     *
     * @param string $orderBy
     * @param string $direction
     * @return $this
     */
    public function defaultOrdering($orderBy, $direction = 'asc')
    {
        $this->order_by = [$orderBy, $direction];

        return $this;
    }

    public function limit($limit, $offset = '') {
        $this->limit = array($limit,$offset);

        return $this;
    }

    /**
     * Filter datagrid with an extra where statement.
     *
     * @param string $field
     * @param string|null $value
     * @param bool $escape
     * @return $this
     */
    public function where(string $field, $value = null, $escape = true)
    {
        $this->where[] = array($field, $value, $escape);

        return $this;
    }

    /**
     * @param string $field
     * @param string $match
     * @param string $side
     * @return $this
     */
    public function like(string $field, $match = '', $side = 'both')
    {
        $this->like[] = array($field, $match, $side);

        return $this;
    }

    protected function _initialize_helpers()
    {
        helper('url');
        helper('form');
    }

    protected function _initialize_variables()
    {
        $this->config = (object)array();

        $config = new \Config\GroceryCrud();

        /** Initialize all the config variables into this object */
        $this->config->default_language 	= $config->default_language;
        $this->config->date_format 			= $config->date_format;
        $this->config->default_per_page		= $config->default_per_page;
        $this->config->default_text_editor	= $config->default_text_editor;
        $this->config->text_editor_type		= $config->text_editor_type;
        $this->config->character_limiter	= $config->character_limiter;
        $this->config->paging_options		= $config->paging_options;
        $this->config->default_theme        = $config->default_theme;
        $this->config->environment          = $config->environment;
        $this->config->xss_clean            = $config->xss_clean;

        /** Initialize default paths */
        $this->default_javascript_path				= $this->default_assets_path.'/js';
        $this->default_css_path						= $this->default_assets_path.'/css';
        $this->default_texteditor_path 				= $this->default_assets_path.'/texteditor';
        $this->default_theme_path					= $this->default_assets_path.'/themes';

        $this->character_limiter = $this->config->character_limiter;

        if ($this->character_limiter === 0 || $this->character_limiter === '0') {
            $this->character_limiter = 1000000; //a very big number
        } elseif($this->character_limiter === null || $this->character_limiter === false) {
            $this->character_limiter = 30; //is better to have the number 30 rather than the 0 value
        }

        if ($this->theme === null && !empty($this->config->default_theme)) {
            $this->setTheme($this->config->default_theme);
        }
    }

    protected function _set_primary_keys_to_model()
    {
        if(!empty($this->primary_keys))
        {
            foreach($this->primary_keys as $primary_key)
            {
                $this->basic_model->set_primary_key($primary_key['field_name'],$primary_key['table_name']);
            }
        }
    }

    /**
     * Initialize all the required libraries and variables before rendering
     */
    protected function pre_render()
    {
        $this->_initialize_variables();
        $this->_initialize_helpers();
        $this->_load_language();
        $this->state_code = $this->getStateCode();

        if ($this->basic_model === null) {
            $this->set_default_Model();
        }

        $this->set_basic_db_table($this->get_table());

        $this->_load_date_format();

        $this->_set_primary_keys_to_model();
    }

    /**
     * Or else "make it work"! The web application takes decision of what to do and show it to the final user.
     * Without this function nothing works. Here is the core of grocery CRUD project where all the decisions are made.
     * I just hope life was that easy as well :)
     *
     * @return object
     * @throws Exception
     */
    public function render()
    {
        $this->pre_render();

        if( $this->state_code != 0 )
        {
            $this->state_info = $this->getStateInfo();
        }
        else
        {
            throw new Exception('The state is unknown , I don\'t know what I will do with your data!', 4);
            die();
        }

        switch ($this->state_code) {
            case 15://success
            case 1://list
                if($this->unset_list)
                {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();

                $this->showList(false,$state_info);

                break;

            case 2://add
                if($this->unset_add)
                {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $this->showAddForm();

                break;

            case 3://edit
                if($this->unset_edit)
                {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();

                $this->showEditForm($state_info);

                break;

            case 4://delete
                if($this->unset_delete)
                {
                    throw new Exception('This user is not allowed to do this operation', 14);
                    die();
                }

                $state_info = $this->getStateInfo();
                $delete_result = $this->db_delete($state_info);

                $this->delete_layout( $delete_result );
                break;

            case 5://insert
                if($this->unset_add)
                {
                    throw new Exception('This user is not allowed to do this operation', 14);
                    die();
                }

                $state_info = $this->getStateInfo();
                $insert_result = $this->db_insert($state_info);

                $this->insert_layout($insert_result);
                break;

            case 6://update
                if($this->unset_edit)
                {
                    throw new Exception('This user is not allowed to do this operation', 14);
                    die();
                }

                $state_info = $this->getStateInfo();
                $update_result = $this->db_update($state_info);

                $this->update_layout( $update_result,$state_info);
                break;

            case 7://ajax_list

                if($this->unset_list)
                {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();
                $this->set_ajax_list_queries($state_info);

                $this->showList(true);

                break;

            case 8://ajax_list_info

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();
                $this->set_ajax_list_queries($state_info);

                $this->showListInfo();
                break;

            case 9://insert_validation

                $validation_result = $this->db_insert_validation();

                $this->validation_layout($validation_result);
                break;

            case 10://update_validation

                $validation_result = $this->db_update_validation();

                $this->validation_layout($validation_result);
                break;
            case 16: //export to excel
                //a big number just to ensure that the table characters will not be cutted.
                $this->character_limiter = 1000000;

                if($this->unset_export)
                {
                    throw new Exception('You don\'t have permissions for this operation', 15);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();
                $this->set_ajax_list_queries($state_info);
                $this->exportToExcel($state_info);
                break;

            case 17: //print
                //a big number just to ensure that the table characters will not be cutted.
                $this->character_limiter = 1000000;

                if($this->unset_print)
                {
                    throw new Exception('You don\'t have permissions for this operation', 15);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();
                $this->set_ajax_list_queries($state_info);
                $this->print_webpage($state_info);
                break;

            case grocery_CRUD_States::STATE_READ:
                if($this->unset_read)
                {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if($this->theme === null)
                    $this->setTheme($this->default_theme);
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();

                $this->showReadForm($state_info);

                break;

            case grocery_CRUD_States::STATE_DELETE_MULTIPLE:

                if($this->unset_delete)
                {
                    throw new Exception('This user is not allowed to do this operation');
                    die();
                }

                $state_info = $this->getStateInfo();
                $delete_result = $this->db_multiple_delete($state_info);

                $this->delete_layout($delete_result);

                break;


            case grocery_CRUD_States::STATE_CLONE:
                if ($this->unset_clone) {
                    throw new Exception('You don\'t have permissions for this operation', 14);
                    die();
                }

                if ($this->theme === null) {
                    $this->setTheme($this->default_theme);
                }
                $this->setThemeBasics();

                $this->set_basic_Layout();

                $state_info = $this->getStateInfo();

                $this->showCloneForm($state_info);

                break;


        }

        return $this->get_layout();
    }

    protected function get_common_data()
    {
        $data = (object)array();

        $data->subject 				= $this->subject;
        $data->subject_plural 		= $this->subject_plural;

        return $data;
    }

    /**
     * The callback is used in cases we need to add or change data before the insert functionality.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackBeforeInsert(callable $callback)
    {
        $this->callback_before_insert = $callback;

        return $this;
    }

    /**
     * The callback that will be used right after the insert of the data.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackAfterInsert(callable $callback)
    {
        $this->callback_after_insert = $callback;

        return $this;
    }

    /**
     * The callback is used when we need to replace the default functionality of the insert.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackInsert(callable $callback)
    {
        $this->callback_insert = $callback;

        return $this;
    }

    /**
     * The callback is used in cases we need to add or change data before the update functionality.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackBeforeUpdate(callable $callback)
    {
        $this->callback_before_update = $callback;

        return $this;
    }

    /**
     * The callback that will be used right after the update of the data.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackAfterUpdate(callable $callback)
    {
        $this->callback_after_update = $callback;

        return $this;
    }

    /**
     * The callback is used when we need to replace the default update functionality.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackUpdate(callable $callback)
    {
        $this->callback_update = $callback;

        return $this;
    }

    /**
     * The callback will be triggered before the delete functionality.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackBeforeDelete(callable $callback)
    {
        $this->callback_before_delete = $callback;

        return $this;
    }

    /**
     * The callback that will be used right after the delete.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackAfterDelete(callable $callback)
    {
        $this->callback_after_delete = $callback;

        return $this;
    }

    /**
     * The basic usage of callbackDelete is when we want to replace the default delete functionality.
     *
     * @param callable $callback
     * @return $this
     */
    public function callbackDelete(callable $callback)
    {
        $this->callback_delete = $callback;

        return $this;
    }

    /**
     * @param null $callback
     * @return $this
     */
    public function callback_before_clone($callback = null)
    {
        $this->callback_before_clone = $callback;

        return $this;
    }

    /**
     * @param null $callback
     * @return $this
     */
    public function callback_after_clone($callback = null)
    {
        $this->callback_after_clone = $callback;

        return $this;
    }

    /**
     * @param null $callback
     * @return $this
     */
    public function callback_clone($callback = null)
    {
        $this->callback_clone = $callback;

        return $this;
    }

    /**
     * The method callbackColumn is the transformation of the data for a column at the datagrid.
     *
     * @param string $column
     * @param callable|null $callback
     * @return $this
     */
    public function callbackColumn(string $column , callable $callback = null)
    {
        $this->callback_column[$column] = $callback;

        return $this;
    }

    /**
     * A callback that is used in case you need to create a custom field for the add form
     *
     * @param string $field
     * @param callable $callback
     * @return $this
     */
    public function callbackAddField(string $field, callable $callback)
    {
        $this->callback_add_field[$field] = $callback;

        return $this;
    }

    /**
     * A callback that is used in case you need to create a custom field for the edit/update form
     *
     * @param string $field
     * @param callable $callback
     * @return $this
     */
    public function callbackEditField(string $field, callable $callback)
    {
        $this->callback_edit_field[$field] = $callback;

        return $this;
    }

    /**
     * A callback that is used in case you need to create a custom field for the clone form
     *
     * @param string $field
     * @param callable $callback
     * @return $this
     */
    public function callbackCloneField(string $field, callable $callback)
    {
        $this->callback_clone_field[$field] = $callback;

        return $this;
    }

    /**
     * This is a callback in order to create a custom field at the read/view form
     *
     * @param string $field
     * @param callable $callback
     * @return $this
     */
    public function callbackReadField(string $field, callable $callback)
    {
        $this->callback_read_field[$field] = $callback;

        return $this;
    }

    /**
     *
     * Gets the basic database table of our crud.
     * @return string
     */
    public function get_table()
    {
        if($this->basic_db_table_checked) {
            return $this->basic_db_table;
        }

        if ($this->basic_db_table === null) {
            throw new Exception('The table name can\'t be empty. Please use setTable to add a basic table name');
        }

        if(!$this->table_exists($this->basic_db_table)) {
            throw new Exception('The table name does not exist. Please check you database and try again.',11);
        }
        $this->basic_db_table_checked = true;
        return $this->basic_db_table;
    }

    /**
     * The most common validation. Checks is the field provided by the user is empty.
     *
     * @param array $requiredFields
     * @return $this
     */
    public function requiredFields(array $requiredFields)
    {
        $this->required_fields = $requiredFields;

        return $this;
    }

    /**
     * Add the fields that they are as UNIQUE in the database structure
     *
     * @return grocery_CRUD
     */

    /**
     * Check if the data for the specified fields are unique. This is used at the insert and the update operation.
     *
     * @param array $uniqueFields
     * @return $this
     */
    public function uniqueFields(array $uniqueFields)
    {
        $this->_unique_fields = $uniqueFields;

        return $this;
    }

    /**
     * Sets the basic database table that we will get our data.
     *
     * @param string $tableName
     * @return $this
     * @throws Exception
     */
    public function setTable(string $tableName)
    {
        if ($tableName === '') {
            throw new Exception('The table name cannot be empty.', 2);
        }

        $this->basic_db_table = $tableName;

        return $this;
    }

    /**
     * Set a full URL path to this method.
     *
     * This method is useful when the path is not specified correctly.
     * Especially when we are using routes.
     * For example:
     * Let's say we have the path http://www.example.com/ however the original url path is
     * http://www.example.com/example/index . We have to specify the url so we can have
     * all the CRUD operations correctly.
     * The url path has to be set from this method like this:
     * <code>
     * 		$crud->setApiUrlPath(site_url('/example/index'), site_url('/'));
     * </code>
     *
     * @param string $crudUrlPath
     * @param string|null $listUrlPath
     * @return grocery_CRUD
     */
    public function setApiUrlPath($crudUrlPath, $listUrlPath = null)
    {
        $this->crud_url_path = $crudUrlPath;

        //If the list_url_path is empty so we are guessing that the list_url_path
        //will be the same with crud_url_path
        $this->list_url_path = !empty($listUrlPath) ? $listUrlPath : $crudUrlPath;

        return $this;
    }

    /**
     * Set a subject to understand what type of CRUD you use.
     * ----------------------------------------------------------------------------------------------
     * Subject_plural: Sets the subject to its plural form. For example the plural
     * of "Customer" is "Customers", "Product" is "Products"... e.t.c.
     *
     * @example Let's say that the table name is db_categories. The $subject will be the 'Category'
     * and the $subjectPlural will be 'Categories'
     * @param string $subject
     * @param null $subjectPlural
     * @return $this
     */
    public function setSubject(string $subject, $subjectPlural = null)
    {
        $this->subject = $subject;
        $this->subject_plural 	= $subjectPlural === null ? $subject : $subjectPlural;

        return $this;
    }

    /**
     * Adding extra action buttons to the rows of the datagrid.
     *
     * @param string $label
     * @param string $cssClass
     * @param callable|null $urlCallback
     * @param bool $newTab
     * @return $this
     */
    public function setActionButton(string $label, string $cssClass, callable $urlCallback, $newTab = false)
    {
        $this->actions[]  = (object)array(
            'label' 		=> $label,
            'image_url' 	=> '',
            'link_url'		=> '',
            'css_class' 	=> $cssClass,
            'url_callback' 	=> $urlCallback,
            'url_has_http'	=> false,
            'new_tab'       => $newTab
        );

        return $this;
    }

    /**
     *
     * Set a simple 1-n foreign key relation
     * @param string $fieldName
     * @param string $relatedTable
     * @param string $relatedTitleField
     * @param mixed $whereClause
     * @param string $orderBy
     * @return Grocery_CRUD
     */
    public function setRelation($fieldName , $relatedTable, $relatedTitleField, $whereClause = null, $orderBy = null)
    {
        $this->relation[$fieldName] = array($fieldName, $relatedTable,$relatedTitleField, $whereClause, $orderBy);
        return $this;
    }

    /**
     *
     * Sets a relation with n-n relationship.
     * @param string $field_name
     * @param string $relation_table
     * @param string $selection_table
     * @param string $primary_key_alias_to_this_table
     * @param string $primary_key_alias_to_selection_table
     * @param string $title_field_selection_table
     * @param string $priority_field_relation_table
     * @param mixed $where_clause
     * @return $this
     */
    public function setRelationNtoN($field_name, $relation_table, $selection_table, $primary_key_alias_to_this_table, $primary_key_alias_to_selection_table , $title_field_selection_table , $where_clause = null)
    {
        $this->relation_n_n[$field_name] =
            (object)array(
                'field_name' => $field_name,
                'relation_table' => $relation_table,
                'selection_table' => $selection_table,
                'primary_key_alias_to_this_table' => $primary_key_alias_to_this_table,
                'primary_key_alias_to_selection_table' => $primary_key_alias_to_selection_table ,
                'title_field_selection_table' => $title_field_selection_table,
                'where_clause' => $where_clause
            );

        return $this;
    }
}