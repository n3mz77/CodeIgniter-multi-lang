<?
/**
 *	Global_model
 *
 */
class Global_model extends CI_Model 
{
/*------------------------------------------------------------------------
 *	Get Footer Links 
 *
 *	@access	public
 *	@param	lang	- lang 
 *	@return	redirect on fail
------------------------------------------------------------------------*/	
	function getFooterLinks($lang) 
	{
		$query = $this->db->query(
			"SELECT 
				parent_key.id			AS second_key_id,
				parent_key.parent_id	AS parent_id,
				parent_key.url_slug		AS second_url,
				parent_page.title		AS second_page_title,
				parent_key.list_order	AS second_list_order,
				
				second_key.id			AS parent_key_id,
				second_key.url_slug		AS parent_url,
				second_page.title		AS parent_page_title,
				second_key.list_order	AS parent_list_order
				
				FROM pages_key AS second_key
				LEFT JOIN pages_key AS parent_key ON parent_key.parent_id = second_key.id
				LEFT JOIN content_key AS second_page ON second_page.key_id = second_key.id
				LEFT JOIN content_key AS parent_page ON parent_page.key_id = parent_key.id
				WHERE parent_key.parent_id != 0 
				AND parent_page.lang = '".$lang."' 
				AND second_page.lang = '".$lang."' 
				AND parent_page.status = '1'
				AND second_page.status = '1'
				AND parent_key.status = '1'
				AND second_key.status = '1'
				AND (parent_key.section = 'test-types' 
					OR parent_key.section = 'app-types' 
					OR parent_key.section = 'platform-devices' )
				ORDER BY second_key.list_order ASC, 
					parent_key.list_order ASC");
					//echo $this->db->last_query();
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
 *	Get Footer Links 
 *
 *	@access	public
 *	@param	string	 
 *	@return	array
------------------------------------------------------------------------*/	
	function searchResults($keyword, $lang, $limit, $offset) 
	{
		$offset = ($offset - 1) * $limit; 

		/*
			70 - dev center blog
			50 - dev center
			36 - benefits
			51 - faq
		*/

		//	pages query
		$pagesSelect = "SELECT 
				pages_key.id			AS pages_key_id,
				pages_key.parent_id	AS key_parent_id,
				pages_key.url_slug	AS key_url_slug,
				pages_key.section		AS key_section,
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
			WHERE content_key.lang = '".$lang."' 
			AND content_key.status = '1'
			AND pages_key.status = '1'
			AND pages_key.id != '70'
			AND pages_key.id != '51'
			AND pages_key.id != '36'
			AND pages_key.id != '50'
			AND ( 
				content_key.title LIKE '%".$keyword."%'
				OR content_key.options LIKE '%".$keyword."%'
			)";
		$pagesQueryTotal 	= $this->db->query($pagesSelect);
		$pagesQuery 		= $this->db->query($pagesSelect." LIMIT ".$offset.", ".$limit);
		if($pagesQuery->num_rows() > 0) {
			$pages['total'] 		= $pagesQuery->num_rows();
			$pages['data'] 			= $pagesQuery->result();
			$pages['status'] 		= true;
			$pages['total_count'] 	= $pagesQueryTotal->num_rows();
		} else {
			$pages['total'] 		= 0;
			$pages['data'] 			= array();
			$pages['status'] 		= false;
			$pages['total_count'] 	= 0;
		}

		//	blog query 
		$blogSelect = "SELECT * FROM blog_posts
			WHERE lang = '".$lang."' 
			AND status = '1'
			AND ( 
				title LIKE '%".$keyword."%'
				OR content LIKE '%".$keyword."%'
			)";

		$blogQueryTotal = $this->db->query($blogSelect);
		$blogQuery 		= $this->db->query($blogSelect." LIMIT ".$offset.", ".$limit);
		if($blogQuery->num_rows() > 0) {
			$blog['total'] 			= $blogQuery->num_rows();
			$blog['data'] 			= $blogQuery->result();
			$blog['status'] 		= true;
			$blog['total_count'] 	= $blogQueryTotal->num_rows();
		} else {
			$blog['total'] 			= 0;
			$blog['data'] 			= array();
			$blog['status'] 		= false;
			$blog['total_count'] 	= 0;
		}

		$result['total']		= $pages['total'] + $blog['total'];
		$result['data']			= array_merge($pages['data'], $blog['data']);
		$result['total_count']	= $pages['total_count'] + $blog['total_count'];

		return $result;
	}
}
?>