<?php namespace App\Models;
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
 * @copyright  	Copyright (c) 2010 through 2012, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @version    	1.4.2
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

use CodeIgniter\Model;

// ------------------------------------------------------------------------

/**
 * Grocery CRUD Model
 *
 *
 * @package    	grocery CRUD
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @version    	1.5.6
 * @link		http://www.grocerycrud.com/documentation
 */
class GroceryCrudModel extends Model {

	protected $primary_key = null;
	protected $table_name = null;
	protected $relation = array();
	protected $relation_n_n = array();
	protected $primary_keys = array();
	protected $builder = null;

    function db_table_exists($table_name = null)
    {
    	return $this->db->tableExists($table_name);
    }

    public function setBuilder($tableName) {
        $this->builder = $this->db->table($tableName);
    }

    function get_list()
    {
    	if($this->table_name === null)
    		return false;

    	$select = "`{$this->table_name}`.*";

    	//set_relation special queries
    	if(!empty($this->relation))
    	{
    		foreach($this->relation as $relation)
    		{
    			list($field_name , $related_table , $related_field_title) = $relation;
    			$unique_join_name = $this->_unique_join_name($field_name);
    			$unique_field_name = $this->_unique_field_name($field_name);

				if(strstr($related_field_title,'{'))
				{
					$related_field_title = str_replace(" ","&nbsp;",$related_field_title);
    				$select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE({$unique_join_name}.",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $unique_field_name";
				}
    			else
    			{
    				$select .= ", $unique_join_name.$related_field_title AS $unique_field_name";
    			}

    			if($this->field_exists($related_field_title))
    				$select .= ", `{$this->table_name}`.$related_field_title AS '{$this->table_name}.$related_field_title'";
    		}
    	}

    	//set_relation_n_n special queries. We prefer sub queries from a simple join for the relation_n_n as it is faster and more stable on big tables.
    	if(!empty($this->relation_n_n))
    	{
			$select = $this->relationNtoNQueries($select);
    	}

        $this->builder = $this->builder->select($select, false);

    	$results = $this->builder->get()->getResult();

    	$this->builder = null;

    	return $results;
    }

    public function get_row()
    {
    	return $this->builder->get()->getRow();
    }

    public function set_primary_key($field_name, $table_name = null)
    {
    	$table_name = $table_name === null ? $this->table_name : $table_name;

    	$this->primary_keys[$table_name] = $field_name;
    }

    protected function relationNtoNQueries($select)
    {
    	$this_table_primary_key = $this->get_primary_key();
    	foreach($this->relation_n_n as $relation_n_n)
    	{
    		list($field_name, $relation_table, $selection_table, $primary_key_alias_to_this_table,
    					$primary_key_alias_to_selection_table, $title_field_selection_table) = array_values((array)$relation_n_n);

    		$primary_key_selection_table = $this->get_primary_key($selection_table);

	    	$field = "";
	    	$use_template = strpos($title_field_selection_table,'{') !== false;
	    	$field_name_hash = $this->_unique_field_name($title_field_selection_table);
	    	if($use_template)
	    	{
	    		$title_field_selection_table = str_replace(" ", "&nbsp;", $title_field_selection_table);
	    		$field .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$title_field_selection_table))."')";
	    	}
	    	else
	    	{
	    		$field .= "$selection_table.$title_field_selection_table";
	    	}

    		//Sorry Codeigniter but you cannot help me with the subquery!
    		$select .= ", (SELECT GROUP_CONCAT(DISTINCT $field) FROM $selection_table "
    			."LEFT JOIN $relation_table ON $relation_table.$primary_key_alias_to_selection_table = $selection_table.$primary_key_selection_table "
    			."WHERE $relation_table.$primary_key_alias_to_this_table = `{$this->table_name}`.$this_table_primary_key GROUP BY $relation_table.$primary_key_alias_to_this_table) AS $field_name";
    	}

    	return $select;
    }

    function order_by($order_by , $direction)
    {
    	$this->builder->orderBy($order_by , $direction);
    }

    function where($key, $value = NULL, $escape = TRUE)
    {
    	$this->builder->where( $key, $value, $escape);
    }

    function or_where($key, $value = NULL, $escape = TRUE)
    {
    	$this->builder->or_where( $key, $value, $escape);
    }

    function having($key, $value = NULL, $escape = TRUE)
    {
    	$this->builder->having( $key, $value, $escape);
    }

    function or_having($key, $value = NULL, $escape = TRUE)
    {
    	$this->builder->or_having( $key, $value, $escape);
    }

    function like($field, $match = '', $side = 'both')
    {
    	$this->builder->like($field, $match, $side);
    }

    function or_like($field, $match = '', $side = 'both')
    {
    	$this->builder->or_like($field, $match, $side);
    }

    function limit($value, $offset = null)
    {
        $this->builder = $this->builder->limit( $value , $offset );
    }

    function get_total_results()
    {
        $totalResults = $this->builder->countAllResults($this->table_name);

        $this->builder = null;

        return $totalResults;
    }

    function set_basic_table($table_name = null)
    {
    	if (!($this->db->tableExists($table_name))) {
            return false;
        }

    	$this->table_name = $table_name;

    	return true;
    }

    function get_edit_values($primary_key_value)
    {
    	$primary_key_field = $this->get_primary_key();
    	$result = $this->builder
            ->where($primary_key_field, $primary_key_value)
            ->get()
            ->getRow();
    	return $result;
    }

    function join_relation($field_name , $related_table , $related_field_title)
    {
		$related_primary_key = $this->get_primary_key($related_table);

		if($related_primary_key !== false)
		{
			$unique_name = $this->_unique_join_name($field_name);
            $this->builder = $this->builder->join( $related_table.' as '.$unique_name , "$unique_name.$related_primary_key = {$this->table_name}.$field_name",'left');

			$this->relation[$field_name] = array($field_name , $related_table , $related_field_title);

			return true;
		}

    	return false;
    }

    function set_relation_n_n_field($field_info)
    {
		$this->relation_n_n[$field_info->field_name] = $field_info;
    }

    protected function _unique_join_name($field_name)
    {
    	return 'j'.substr(md5($field_name),0,8); //This j is because is better for a string to begin with a letter and not with a number
    }

    protected function _unique_field_name($field_name)
    {
    	return 's'.substr(md5($field_name),0,8); //This s is because is better for a string to begin with a letter and not with a number
    }

    function get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, $limit = null, $search_like = null)
    {
        $this->builder = $this->db->table($related_table);

    	$relation_array = array();
    	$field_name_hash = $this->_unique_field_name($field_name);

    	$related_primary_key = $this->get_primary_key($related_table);

    	$select = "$related_table.$related_primary_key, ";

    	if(strstr($related_field_title,'{'))
    	{
    		$related_field_title = str_replace(" ", "&nbsp;", $related_field_title);
    		$select .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $field_name_hash";
    	}
    	else
    	{
	    	$select .= "$related_table.$related_field_title as $field_name_hash";
    	}

    	$this->builder->select($select,false);
    	if($where_clause !== null)
            $this->builder->where($where_clause);

    	if($where_clause !== null)
            $this->builder->where($where_clause);

    	if($limit !== null)
            $this->builder->limit($limit);

    	if($search_like !== null)
            $this->builder->having("$field_name_hash LIKE '%".$this->db->escape_like_str($search_like)."%'");

    	$order_by !== null
    		? $this->builder->orderBy($order_by)
    		: $this->builder->orderBy($field_name_hash);

    	$results = $this->builder->get()->getResult();

    	foreach($results as $row)
    	{
    		$relation_array[$row->$related_primary_key] = $row->$field_name_hash;
    	}

    	return $relation_array;
    }

    function get_ajax_relation_array($search, $field_name , $related_table , $related_field_title, $where_clause, $order_by)
    {
    	return $this->get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, 10 , $search);
    }

    function get_relation_total_rows($field_name , $related_table , $related_field_title, $where_clause)
    {
        $this->builder = $this->db->table($related_table);

    	if($where_clause !== null) {
            $this->builder = $this->builder->where($where_clause);
        }

    	$countAllResults = $this->builder->countAllResults($related_table);

        $this->builder = null;

        return $countAllResults;
    }

    function get_relation_n_n_selection_array($primary_key_value, $field_info)
    {
    	$select = '';
    	$related_field_title = $field_info->title_field_selection_table;
    	$use_template = strpos($related_field_title,'{') !== false;;
    	$field_name_hash = $this->_unique_field_name($related_field_title);
    	if($use_template)
    	{
    		$related_field_title = str_replace(" ", "&nbsp;", $related_field_title);
    		$select .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $field_name_hash";
    	}
    	else
    	{
    		$select .= "$related_field_title as $field_name_hash";
    	}
    	$this->builder = $this->db->table($field_info->relation_table);
        $this->builder = $this->builder->select('*, '.$select,false);

    	$selection_primary_key = $this->get_primary_key($field_info->selection_table);

        if(!$use_template){
            $this->builder = $this->builder->orderBy("{$field_info->selection_table}.{$field_info->title_field_selection_table}");
        }

        $this->builder = $this->builder->where($field_info->primary_key_alias_to_this_table, $primary_key_value)
            ->join(
    			$field_info->selection_table,
    			"{$field_info->relation_table}.{$field_info->primary_key_alias_to_selection_table} = {$field_info->selection_table}.{$selection_primary_key}"
    		);
    	$results = $this->builder->get()->getResult();

    	$this->builder = null;

    	$results_array = array();
    	foreach($results as $row)
    	{
    		$results_array[$row->{$field_info->primary_key_alias_to_selection_table}] = $row->{$field_name_hash};
    	}

    	return $results_array;
    }

    function get_relation_n_n_unselected_array($field_info, $selected_values)
    {
    	$use_where_clause = !empty($field_info->where_clause);

    	$select = "";
    	$related_field_title = $field_info->title_field_selection_table;
    	$use_template = strpos($related_field_title,'{') !== false;
    	$field_name_hash = $this->_unique_field_name($related_field_title);

    	if($use_template)
    	{
    		$related_field_title = str_replace(" ", "&nbsp;", $related_field_title);
    		$select .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $field_name_hash";
    	}
    	else
    	{
    		$select .= "$related_field_title as $field_name_hash";
    	}

        $this->builder = $this->db->table($field_info->selection_table);
    	$this->builder = $this->builder->select('*, ' . $select, false);

    	if($use_where_clause){
            $this->builder = $this->builder->where($field_info->where_clause);
    	}

    	$selection_primary_key = $this->get_primary_key($field_info->selection_table);
        if(!$use_template) {
            $this->builder = $this->builder->orderBy("{$field_info->selection_table}.{$field_info->title_field_selection_table}");
        }
        $results = $this->builder->get()->getResult();

        $this->builder = null;

        $results_array = array();
        foreach($results as $row)
        {
            if(!isset($selected_values[$row->$selection_primary_key]))
                $results_array[$row->$selection_primary_key] = $row->{$field_name_hash};
        }

        return $results_array;
    }

    function db_relation_n_n_update($field_info, $post_data ,$main_primary_key)
    {
        $this->builder = $this->db->table($field_info->relation_table);

        $this->builder->where($field_info->primary_key_alias_to_this_table, $main_primary_key);
    	if(!empty($post_data)) {
            $this->builder->whereNotIn($field_info->primary_key_alias_to_selection_table, $post_data);
        }

        $this->builder->delete();

        $this->builder = $this->db->table($field_info->relation_table);

    	if(!empty($post_data)) {
    		foreach($post_data as $primary_key_value) {
				$insertData = array(
	    			$field_info->primary_key_alias_to_this_table => $main_primary_key,
	    			$field_info->primary_key_alias_to_selection_table => $primary_key_value,
	    		);

                $this->builder->where($insertData);
				$count = $this->builder->countAllResults($field_info->relation_table);

				// Insert data only when they doesn't exist so we will not have duplicates
				if($count === 0) {
                    $this->builder = null;
                    $this->builder = $this->db->table($field_info->relation_table);
                    $this->builder->insert($insertData);
				}
	    	}
    	}
    }

    function db_relation_n_n_delete($field_info, $main_primary_key)
    {
        $this->builder = $this->db->table($field_info->relation_table);
        $this->builder->where($field_info->primary_key_alias_to_this_table, $main_primary_key);
    	$this->builder->delete();
    }

    function get_field_types_basic_table()
    {
    	$db_field_types = array();
    	foreach($this->db->query("SHOW COLUMNS FROM `{$this->table_name}`")->getResult() as $db_field_type)
    	{
    		$type = explode("(",$db_field_type->Type);
    		$db_type = $type[0];

    		if(isset($type[1]))
    		{
    			if(substr($type[1],-1) == ')')
    			{
    				$length = substr($type[1],0,-1);
    			}
    			else
    			{
    				list($length) = explode(" ",$type[1]);
    				$length = substr($length,0,-1);
    			}
    		}
    		else
    		{
    			$length = '';
    		}
    		$db_field_types[$db_field_type->Field]['db_max_length'] = $length;
    		$db_field_types[$db_field_type->Field]['db_type'] = $db_type;
    		$db_field_types[$db_field_type->Field]['db_null'] = $db_field_type->Null == 'YES' ? true : false;
    		$db_field_types[$db_field_type->Field]['db_extra'] = $db_field_type->Extra;
    	}

    	$results = $this->db->getFieldData($this->table_name);
    	foreach($results as $num => $row)
    	{
    		$row = (array)$row;
    		$results[$num] = (object)( array_merge($row, $db_field_types[$row['name']])  );
    	}

    	return $results;
    }

    function get_field_types($table_name)
    {
    	$results = $this->db->getFieldData($table_name);

    	return $results;
    }

    function db_update($post_array, $primary_key_value)
    {
    	$primary_key_field = $this->get_primary_key();
    	return $this->db->table($this->table_name)->update($post_array, array( $primary_key_field => $primary_key_value));
    }

    function db_insert($post_array)
    {
    	$insert = $this->db->table($this->table_name)->insert($post_array);
    	if($insert) {
    		return $this->db->insertID();
    	}
    	return false;
    }

    function db_delete($primary_key_value)
    {
    	$primary_key_field = $this->get_primary_key();

    	if($primary_key_field === false || empty($primary_key_value)) {
            return false;
        }

    	$this->db->table($this->table_name)->delete(array( $primary_key_field => $primary_key_value));

        return true;
    }

    function db_file_delete($field_name, $filename)
    {
    	if( $this->db->update($this->table_name,array($field_name => ''),array($field_name => $filename)) )
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    function field_exists($field,$table_name = null)
    {
    	if(empty($table_name))
    	{
    		$table_name = $this->table_name;
    	}
    	return $this->db->fieldExists($field,$table_name);
    }

    function get_primary_key($table_name = null)
    {
    	if($table_name == null)
    	{
    		if(isset($this->primary_keys[$this->table_name]))
    		{
    			return $this->primary_keys[$this->table_name];
    		}

	    	if(empty($this->primary_key))
	    	{
		    	$fields = $this->get_field_types_basic_table();

		    	foreach($fields as $field)
		    	{
		    		if($field->primary_key == 1)
		    		{
		    			return $field->name;
		    		}
		    	}

		    	return false;
	    	}
	    	else
	    	{
	    		return $this->primary_key;
	    	}
    	}
    	else
    	{
    		if(isset($this->primary_keys[$table_name]))
    		{
    			return $this->primary_keys[$table_name];
    		}

	    	$fields = $this->get_field_types($table_name);

	    	foreach($fields as $field)
	    	{
	    		if($field->primary_key == 1)
	    		{
	    			return $field->name;
	    		}
	    	}

	    	return false;
    	}

    }

    function escape_str($value)
    {
    	return $this->db->escape($value);
    }

}
