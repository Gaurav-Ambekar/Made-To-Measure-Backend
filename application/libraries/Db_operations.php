<?php
	class Db_operations{

		public $CI="";
		public function __construct(){

			$this->CI =& get_instance();
		}

		function data_insert($table='',$arr=''){

			if ($this->CI->db->table_exists($table) )
			{
				$this->CI->db->insert($table,$arr);
				return $this->CI->db->insert_id();
			}
			else
			{
				return -1;			}
		}

		function get_recordlist($table='',$field='',$orderby=''){

			if ($this->CI->db->table_exists($table) )
			{
				if(!empty($orderby)){

					$this->CI->db->order_by($field,$orderby);
				}

				$tdata = $this->CI->db->get($table);
				return $tdata->result_array();
			}
			else
			{
				return -1;			}
		}

		function get_record($table='', $condition){

			if ($this->CI->db->table_exists($table) )
			{
			  return $this->CI->db->get_where($table,$condition)->result_array();
			}
			else
			{
			  return -1;
			}
			
		}

		function data_update($table='',$arr='',$field='',$value=''){

			if ($this->CI->db->table_exists($table) )
			{
				$this->CI->db->where($field,$value);
				return $this->CI->db->update($table,$arr);
			}
			else
			{
				return -1;
			}
		}


		function delete_record($table='',$arr=''){

			if ($this->CI->db->table_exists($table) )
			{
				return $this->CI->db->delete($table,$arr);
			}
			else
			{
				return -1;	
			}
		}

		function get_max_id($table, $field){

			if ($this->CI->db->table_exists($table) )
			{
				$this->CI->db->select_max($field, 'max_id');
				return $this->CI->db->get($table)->result_array()[0]['max_id'];
			}
			else
			{
				return -1;	
			}

			
		}

		function get_cnt($table, $arr){

			if ($this->CI->db->table_exists($table) )
			{
				$this->CI->db->where($arr);
				return $this->CI->db->count_all_results($table);
			}
			else
			{
				return -1;	
			}

			
		}

		function empty_table($table){

			if ($this->CI->db->table_exists($table) )
			{
				return $this->CI->db->empty_table($table); 
			}
			else
			{
				return -1;	
			}
			
		}

		function get_fin_year_max_id($table, $field, $field2)
		{
			if ($this->CI->db->table_exists($table) )
			{
				$f_year = $this->CI->session->userdata('fin_year');    
				$record = $this->CI->db->query("SELECT MAX($field) as max_id FROM $table WHERE $field2 = '$f_year'")->result_array();

				if(empty($record[0]['max_id']))
				{
					return 1;
				}
				else
				{
					return $record[0]['max_id']+1;
				}
			}
			else
			{
				return -1;	
			}
			

		}
		public function get_max_id_custom($table, $field)
        {
        	if ($this->CI->db->table_exists($table) )
			{
			}
			else
			{
				return -1;	
			}

            $this->CI->db->select_max($field, 'max_id'); 

            $record = $this->CI->db->get($table)->result_array()[0];
            
            if(empty($record))
            {

                return 1;
            }
            else
            {
                if($record['max_id'] == 0)
                {
                    return 1;
                }
                else
                {
                	return $record['max_id']+1;
                }
                
            }
        }

        function image_autorotate_resize($params = NULL, $resize = TRUE)
		{
			if (!is_array($params) || empty($params))
			{
				return 0;
			}
			$filepath 	= $params['filepath'];
			$exif 		= @exif_read_data($filepath);
			$CI 		=& get_instance();

			$CI->load->library('image_lib');
			$config['image_library'] = 'gd2';
			$config['source_image']	= $filepath;
			if ($resize) 
			{			
				$tmp_filename 			= $filepath;
				list($width, $height) 	= getimagesize($tmp_filename);
				
				if ($width >= $height)
		    	{
		            $config['width'] = 800;
		        }
		        else
		        {
		            $config['height'] = 800;
		        }
				$config['master_dim'] = 'auto';
				$config['create_thumb'] = FALSE;
				$config['maintain_ratio'] = TRUE;
				$config['quality'] = '100%';  
				$config['new_image'] = $filepath;
				$CI->image_lib->initialize($config); 
				$CI->image_lib->resize();
			}
			if (!empty($exif['Orientation']))
			{
				$oris = array();
					
				switch($exif['Orientation'])
				{
				        case 1: // no need to perform any changes
				        break;
				
				        case 2: // horizontal flip
						$oris[] = 'hor';
				        break;
				                                
				        case 3: // 180 rotate left
				        	$oris[] = '180';
				        break;
				                    
				        case 4: // vertical flip
				        	$oris[] = 'ver';
				        break;
				                
				        case 5: // vertical flip + 90 rotate right
				        	$oris[] = 'ver';
						$oris[] = '270';
				        break;
				                
				        case 6: // 90 rotate right
				        	$oris[] = '270';
				        break;
				                
				        case 7: // horizontal flip + 90 rotate right
				        	$oris[] = 'hor';
						$oris[] = '270';
				        break;
				                
				        case 8: // 90 rotate left
				        	$oris[] = '90';
				        break;
						
					default: break;
				}
			
				foreach ($oris as $ori) 
				{
					$config['rotation_angle'] = $ori;
					$CI->image_lib->initialize($config); 				
					$CI->image_lib->rotate();
				}	
			}
		}
		function get_fin_year_max_id_custom($table,$field,$field2,$f_year)
		{
			if ($this->CI->db->table_exists($table) )
			{
				$query = "SELECT MAX($field) as max_id FROM $table WHERE $field2 = '$f_year'";
				// echo "<pre>"; print_r($query); exit;	
				$record = $this->CI->db->query($query)->result_array();
				// echo "<pre>"; print_r($record); exit;	


				if(empty($record[0]['max_id']))
				{
					return 1;
				}
				else
				{
					return $record[0]['max_id']+1;
				}
				// echo "<pre>"; print_r($record); exit;	
			}
			else
			{
				return -1;	
			}
		}

		function get_item_data_by_code($code)
		{
			$item_query="
							SELECT 
								item.item_code as item_no, item.item_desc as bom_item_desc, item.item_shelf_life as bom_shelf_life,
								item.item_sticker as bom_sticker, item.item_status
							FROM item_master item
							WHERE item.item_code = '".$code."'
						";
						// AND menu.menu_menu_active = 1
							// AND menu.menu_active = 1
			// echo "<pre>"; print_r($item_query); exit;
			return $this->CI->db->query($item_query)->result_array();
			

		}
	}
?>