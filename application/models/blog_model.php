<?
/**
 *	Blog Model
 *
 *	Contains functions that use the blog database
 */
class Blog_model extends CI_Model 
{
// --------------------------------------------------------------------
/**
 *	Get Blog Posts
 *
 *	@access	public
 *	@param	string	- language key
 *	@param	string	- category, false when doesn't exist
 *	@param	string	- keyword
 *	@param	int		- offset
 *	@param	int		- limit
 *	@param	bool	- total  
 *	@return	redirect on fail
*/	
	function getBlogPosts($lang, $category, $keyword, $offset, $limit, $total = false) 
	{

		$searchQuery = " AND status = '1' ";
		if( $keyword  ) {
			$searchQuery = "
			AND (`title` LIKE '%".$keyword."%'
			OR `excerpt` LIKE '%".$keyword."%'
			OR `content` LIKE '%".$keyword."%')";
		} 

		//	if category doesn't exist
		if($category == false) {
			$queryStr 		= "SELECT * FROM blog_posts WHERE post_date <= '".date("Y-m-d")."' AND lang = '".$lang."' ".$searchQuery." ORDER BY sticky DESC, post_date DESC LIMIT ".$offset.", ".$limit;
			$queryStrTotal 	= "SELECT * FROM blog_posts WHERE post_date <= '".date("Y-m-d")."' AND lang = '".$lang."' ".$searchQuery;
		
		//	if category exists
		} else {
			$queryStr 		= "SELECT * FROM blog_posts WHERE post_date <= '".date("Y-m-d")."' AND lang = '".$lang."' ".$searchQuery." AND category_id = '".$category."' ORDER BY sticky DESC, post_date DESC LIMIT ".$offset.", ".$limit;
			$queryStrTotal 	= "SELECT * FROM blog_posts WHERE post_date <= '".date("Y-m-d")."' AND lang = '".$lang."' ".$searchQuery." AND category_id = '".$category."'";
		}

		$query 		= $this->db->query($queryStr);
		if($query->num_rows() > 0) {
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		
			$result['total']	= 0;
			if($total == true) {
				$queryTotal 		= $this->db->query($queryStrTotal);
				$result['total']	= $queryTotal->num_rows();
			}
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
			$result['total']	= 0;
		}
		return $result;
	}
// --------------------------------------------------------------------
/**
 *	Get Latest Blog Posts
 *
 *	@access	public
 *	@param	string	- language key
 *	@param	int		- limit
 *	@return	redirect on fail
*/	
	function getLatestBlogPosts($lang, $limit) 
	{
		$queryStr 	= "SELECT 
			blog_posts.id, 
			blog_posts.post_date, 
			blog_posts.title, 
			blog_posts.excerpt, 
			blog_posts.url_slug,
			blog_categories.slug AS category_slug
		 	FROM blog_posts 
			LEFT JOIN blog_categories ON blog_posts.category_id = blog_categories.id 
			WHERE blog_posts.post_date <= '".date("Y-m-d")."' 
			AND blog_posts.lang = '".$lang."' 
			AND blog_posts.status = '1'
			ORDER BY blog_posts.sticky DESC, blog_posts.post_date DESC LIMIT ".$limit;
		$query 		= $this->db->query($queryStr);
		if($query->num_rows() > 0) {
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
// --------------------------------------------------------------------
/**
 *	Get Related Blog Posts
 *
 *	@access	public
 *	@param	string	- language key
 *	@param	int		- category
 *	@param	int		- id
 *	@param	int		- limit
 *	@return	redirect on fail
*/	
	function getRelatedBlogPosts($lang, $category, $id, $limit) 
	{
		$queryStr 	= "SELECT 
			post_date, 
			title, 
			id, 
			url_slug
			FROM blog_posts 
			WHERE post_date <= '".date("Y-m-d")."' 
			AND lang = '".$lang."' 
			AND status = '1'
			AND id != '".$id."'
			AND category_id = '".$category."' 
			ORDER BY post_date DESC LIMIT ".$limit;
		$query 		= $this->db->query($queryStr);
		if($query->num_rows() > 0) {
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
// --------------------------------------------------------------------
/**
 *	Get Blog Posts by Date
 * 
 *	use this when there are no results for related blog posts
 *
 *	@access	public
 *	@param	string	- language key
 *	@param	int		- id
 *	@param	int		- limit
 *	@return	redirect on fail
*/	
	function getBlogPostsByDate($lang, $id, $limit) 
	{
		$queryStr 	= "SELECT 
			post_date, 
			title, 
			id, 
			url_slug
			FROM blog_posts 
			WHERE post_date <= '".date("Y-m-d")."' 
			AND lang = '".$lang."' 
			AND status = '1'
			AND id != '".$id."'
			ORDER BY post_date DESC LIMIT ".$limit;
		$query 		= $this->db->query($queryStr);
		if($query->num_rows() > 0) {
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}
// --------------------------------------------------------------------
/**
 *	Get Single Blog
 *
 *	@access	public
 *	@param	date		- date
 *	@param	int			- key id 
 *	@param	string		- url slug
 *	@param	bool		- preview  
 *	@return	array 
*/	
	function getSingleBlogPost($lang, $date, $key_id, $id = 0, $url_slug, $preview = false) 
	{
		$statusQuery = " AND status = '1' ";
		if( $preview == true ) {	
			$statusQuery = "";
		}
		$query = $this->db->query("
			SELECT * FROM blog_posts
			WHERE lang = '".$lang."' AND key_id = '".$key_id."' AND url_slug = '".$url_slug."' ".$statusQuery."
			LIMIT 1");
		if($query->num_rows() == 1) {
			$result['data'] 	= $query->row();

			//	check date, if date doesn't match, then redirect
			if($date != $result['data']->post_date) {
				$dateEx = explode('-', $result['data']->post_date);
				//echo $lang.'/developer-center/blog/'.$dateEx[0].'/'.$dateEx[1].'/'.$dateEx[2].'/'.$key_id.'/'.$url_slug;
				redirect('/developer-center/blog/'.$dateEx[0].'/'.$dateEx[1].'/'.$dateEx[2].'/'.$key_id.'/'.$url_slug);
			}
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}

// --------------------------------------------------------------------
/**
 *	Get Blog Categories
 *
 *	Check if blog category exists
 *
 *	@access	public
 *	@param	string 	language
 *	@return	array 	
*/	
	function getBlogCategories($lang) 
	{
		$query = $this->db->query("SELECT 
			blog_posts.category_id AS category_id, 
			blog_categories.name AS name, 
			blog_categories.slug AS url, 
			COUNT( * ) AS totalCount 
			FROM blog_posts
			LEFT JOIN blog_categories ON blog_posts.category_id = blog_categories.id  
			WHERE blog_posts.lang = '".$lang."' 
			AND blog_posts.status = '1'
			AND blog_posts.category_id != '0'
			GROUP BY blog_posts.category_id");
		if($query->num_rows() > 0) {
			$result['data'] 	= $query->result();
			$result['status'] 	= true;
		} else {
			$result['data'] 	= null;
			$result['status'] 	= false;
		}
		return $result;
	}



// --------------------------------------------------------------------
/**
 *	Get Blog Category Data
 *
 *	Check if blog category exists
 *
 *	@access	public
 *	@param	string 	language
 *	@param	string 	url
 *	@return	array 	
*/	
	function getBlogCategoryData($lang, $url) 
	{
		$query = $this->db->query("SELECT * FROM blog_categories WHERE lang = '".$lang."' AND slug = '".$url."' LIMIT 1");
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