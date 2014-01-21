<?
/**
 *	Pages Model
 *
 *	Contains functions that use the page database
 */
class Pages_model extends CI_Model 
{
/*------------------------------------------------------------------------
 *	Get Page Data for single page
 *
 *	@access	public
 *	@param	int		- key id
 *	@param	lang	- lang 
 *	@return	redirect on fail
------------------------------------------------------------------------*/	
	function getPageData($url, $lang, $preview = false) 
	{
		$statusQuery = " AND content_key.status = '1' ";
		if( $preview == true ) {	
			$statusQuery = "";
		}
		$query = $this->db->query("
			SELECT 
				pages_key.id			AS key_id,
				pages_key.parent_id		AS key_parent_id,
				pages_key.url_slug		AS key_url_slug,
				pages_key.name			AS key_name,
				pages_key.status		AS key_status,
				content_key.id			AS id,
				content_key.lang		AS lang,
				content_key.title		AS title,
				content_key.options		AS options
			FROM pages_key 
			LEFT JOIN content_key ON content_key.key_id = pages_key.id
			WHERE pages_key.url_slug = '".$url."' 
			AND content_key.lang = '".$lang."' ".$statusQuery."
			LIMIT 1");
		if($query->num_rows() == 1) {
			$result['data'] 	= $query->row();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
	
/*------------------------------------------------------------------------
 *	Get Sub Page List
 *
 *	@access	public
 *	@param	int		- key id
 *	@param	lang	- lang 
 *	@return	redirect on fail
------------------------------------------------------------------------*/	
	function getSubPageList($id, $lang, $preview = false) 
	{
		$statusQuery = " AND content_key.status = '1' ";
		if( $preview == true ) {	
			$statusQuery = "";
		}
		
		$query = $this->db->query("
			SELECT 
				pages_key.id			AS key_id,
				pages_key.parent_id		AS key_parent_id,
				pages_key.url_slug		AS key_url_slug,
				pages_key.name			AS key_name,
				pages_key.status		AS key_status,
				pages_key.colorTheme	AS color,
				content_key.id			AS id,
				content_key.lang		AS lang,
				content_key.title		AS title,
				content_key.options 	AS options,
				content_key.status 		AS status
			FROM pages_key 
			LEFT JOIN content_key ON content_key.key_id = pages_key.id
			WHERE pages_key.parent_id = '".$id."' 
			AND content_key.lang = '".$lang."' ".$statusQuery."
			ORDER BY pages_key.list_order");
		if($query->num_rows() > 0) {
			$result['total'] 	= $query->num_rows();
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['total'] 	= 0;
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
	
/*------------------------------------------------------------------------
 *	Get Sub Page List
 *
 *	@access	public
 *	@param	int		- section type
 *	@param	lang	- lang 
 *	@return	redirect on fail
------------------------------------------------------------------------*/	
	function getDevCenterSubPageList($id, $lang, $preview = false) 
	{
		$statusQuery = " AND content_key.status = '1' ";
		if( $preview == true ) {	
			$statusQuery = "";
		}
		
		$query = $this->db->query("
			SELECT 
				pages_key.id			AS key_id,
				pages_key.parent_id		AS key_parent_id,
				pages_key.url_slug		AS key_url_slug,
				pages_key.name			AS key_name,
				pages_key.status		AS key_status,
				pages_key.colorTheme	AS color,
				content_key.id			AS id,
				content_key.lang		AS lang,
				content_key.title		AS title,
				content_key.options 	AS options,
				content_key.status 		AS status
			FROM pages_key 
			LEFT JOIN content_key ON content_key.key_id = pages_key.id
			WHERE pages_key.id != '50' AND pages_key.parent_id = '0' AND pages_key.section_type = '2'
			AND content_key.lang = '".$lang."' ".$statusQuery."
			ORDER BY pages_key.list_order");
		if($query->num_rows() > 0) {
			$result['total'] 	= $query->num_rows();
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['total'] 	= 0;
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
/*------------------------------------------------------------------------
 *	Get Sub Page Data
 *
 *	@access	public
 *	@param	int		- key id
 *	@param	lang	- lang 
 *	@return	redirect on fail
------------------------------------------------------------------------*/	
	function getSubPageData($url, $lang, $preview = false) 
	{
		$statusQuery = " AND content_key.status = '1' ";
		if( $preview == true ) {	
			$statusQuery = "";
		}
		
		$query = $this->db->query("
			SELECT 
				pages_key.id				AS key_id,
				pages_key.parent_id			AS key_parent_id,
				pages_key.url_slug			AS key_url_slug,
				pages_key.name				AS key_name,
				pages_key.section			AS section,
				pages_key.colorTheme		AS color,
				pages_key.status			AS key_status,
				content_key.id				AS id,
				content_key.lang			AS lang,
				content_key.title			AS title,
				content_key.options			AS options
			FROM pages_key 
			LEFT JOIN content_key ON content_key.key_id = pages_key.id
			WHERE pages_key.url_slug = '".$url."' ".$statusQuery."
			LIMIT 1");
			//echo $this->db->last_query();
		if($query->num_rows() == 1) {
			$result['data'] 	= $query->row();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}

}
?>