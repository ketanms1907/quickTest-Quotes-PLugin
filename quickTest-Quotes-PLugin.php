<?php
/*
Plugin Name: quickTest Quotes
Description: Plugin for displaying reports
Author: quickTest
Version: 1
Author URI: 
*/

// Deny direct access
defined('ABSPATH') or die("No script kiddies please!");
class export_data_to_csv{

    private $data;
    private $headers;

    function __construct($headersArr, $dataArr, $filename){
        $this->data = $dataArr;  
		$this->headers = $headersArr;   

        $generatedDate = date('d-m-Y His');                         //Date will be part of file name. I dont like to see ...(23).csv downloaded

        $csvFile = $this->generate_csv();                           //Getting the text generated to download
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);                    //Forces the browser to download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . "_" . $generatedDate . ".csv\";" );
        header("Content-Transfer-Encoding: binary");

        echo $csvFile;
        exit;

    }


    function generate_csv(){
         $array = $this->data;      
		 $headers = $this->headers; 
		 if (count($array) == 0) {
		   return null;
		 }
		 ob_end_clean();
		 ob_start();
		 $df = fopen("php://output", 'w');
		 fputcsv($df, array_values($headers));
		 foreach ($array as $row) {
			$row = str_replace(chr(194)," ",$row);
			fputcsv($df, $row);
		 }
		 fclose($df);
		 return ob_get_clean();
	}
}
if(isset($_POST['quote_csv']) || isset($_POST['quote_priority']) && ($_GET['page'] == 'quickTest-quotes' && is_admin()))
{	
	ob_end_clean();
	
	if(isset($_POST['quote_priority'])){
		$quotes = $wpdb->get_results( "SELECT * FROM wp_quote WHERE contact_flag = '1'");
	}else{
		$quotes = $wpdb->get_results( "SELECT * FROM wp_quote");
	}
	
	foreach($quotes as $quote)
	{
		
		/*$quoteArr[] = array(
			'quote_no' => $quote->quoteID,
			'quote_name' => $quote->first_name." ".$quote->surname,
			'quote_email' => $quote->email,
		);*/
		if($quote->contact_flag == 1){
			$priority = 'Yes';
		}else{
			$priority = 'No';
		}
		$quoteArr[] = array(
			'quoteID' => $quote->quoteID,
			'title' => $quote->title,
			'first_name' => $quote->first_name,
			'surname' => $quote->surname,
			'quote' => $quote->quote,
			'address_1' => $quote->address_1,
			'address_2' => $quote->address_2,
			'address_3' => $quote->address_3,
			'post_code' => $quote->post_code,
			'telephone' => $quote->telephone,
			'email' => $quote->email,
			'property_value' => $quote->property_value,
			'created_at' => $quote->created_at,
			'property_type' => $quote->property_type,
			'bedrooms' => $quote->bedrooms,
			'reason_for_sale' => $quote->reason_for_sale,
			'first_viewed_url' => $quote->first_viewed_url,
			'referring_page' => $quote->referring_page,
			'GAclientID' => $quote->GAclientID,
			'Priority' => $priority,
       );
	}	
	
	$headers = array('ID', 'Title', 'FirstName', 'Surname', 'Amount', 'Address1', 'Address2', 'Address3', 'Postcode', 'Tel', 'Email', 'AskingPrice', 'DateCreated', 'PropertyType', 'NumberBeds', 'ReasonSelling', 'FirstViewed', 'PageBeforeEstimate','GAclientID','Priority');

	$exportCSV = new export_data_to_csv($headers,$quoteArr,'quotes_details'); 
	
}

function quickTest_quotes_menu() {
	//add_menu_page( 'quickTest Quotes', 'quickTest Quotes', 'manage_options', 'quickTest-quotes', 'quickTest_quotes_list');
	add_object_page( 'quickTest Quotes List', 'quickTest Quotes List', 'manage_options', 'quickTest-quotes', 'quickTest_quotes_list');
}
add_action('admin_menu', 'quickTest_quotes_menu'); 

function quickTest_quotes_list()
{
	global $wpdb;
	
	if(isset($_GET['quote_id']) && $_GET['action'] == 'view'){
		$entry = $wpdb->get_results( "SELECT * FROM wp_quote WHERE quoteID = ".$_GET['quote_id']."" );
?>

		<div class="wrap">
        <div class="metabox-holder columns-1" id="post-body">
          <div class="edit-form-section edit-comment-section" id="post-body-content">
            <div class="stuffbox" id="namediv">
              <div class="inside">
                <fieldset>
                  <legend class="edit-comment-author"><h1>View Quote</h1></legend>
                  <table class="form-table">
                    <tbody>
                      <tr>
                        <td class="first"><label>Quote Reference:</label></td>
                        <td><?php echo $entry[0]->quoteID ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Title:</label></td>
                        <td><?php echo $entry[0]->title ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>First Name:</label></td>
                        <td><?php echo $entry[0]->first_name ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Surname:</label></td>
                        <td><?php echo $entry[0]->surname ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Telephone:</label></td>
                        <td><?php echo $entry[0]->telephone ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Email:</label></td>
                        <td><?php echo $entry[0]->email ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Address 1:</label></td>
                        <td><?php echo $entry[0]->address_1 ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Address 2:</label></td>
                        <td><?php echo $entry[0]->address_2 ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Address 3:</label></td>
                        <td><?php echo $entry[0]->address_3 ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Post Code:</label></td>
                        <td><?php echo $entry[0]->post_code ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Reason For Sale:</label></td>
                        <td><?php echo $entry[0]->reason_for_sale ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Bedrooms:</label></td>
                        <td><?php echo $entry[0]->bedrooms ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Property Value:</label></td>
                        <td><?php echo $entry[0]->property_value ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Property Type:</label></td>
                        <td><?php echo $entry[0]->property_type ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Quote:</label></td>
                        <td><?php echo $entry[0]->quote ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Date:</label></td>
                        <td><?php echo $entry[0]->created_at ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>Referring Page:</label></td>
                        <td><?php echo $entry[0]->referring_page ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>First Viewed Page:</label></td>
                        <td><?php echo $entry[0]->first_viewed_url ?></td>
                      </tr>
                      <tr>
                        <td class="first"><label>GAclientID:</label></td>
                        <td><?php echo $entry[0]->GAclientID; ?></td>
                      </tr>
                      <tr>
                        <td class="first">&nbsp;</td>
                        <td><a class="button" href="javascript:window.history.go(-1);">Back to Quote List</a></td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset>
              </div>
            </div>
          </div>
      </div>
      </div>
<?php
	}else{
		if(isset($_GET['quote_id']) && isset($_GET['action'])){
			if($_GET['action'] == 'delete'){
				$wpdb->query(
				  'DELETE FROM '.$wpdb->prefix.'quote
				   WHERE quoteID = "'.$_GET['quote_id'].'"'
				);
				echo '<div class="updated notice">
						  <p>Deleted '.$_GET['quote_id'].' quote successfully from database!!</p>
					  </div>';
			}
		}
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		$limit = 20; // number of rows in page
		$offset = ( $pagenum - 1 ) * $limit;
		$total = $wpdb->get_var( "SELECT COUNT(`quoteID`) FROM wp_quote" );
		$num_of_pages = ceil( $total / $limit );
		
		$entries = $wpdb->get_results( "SELECT * FROM wp_quote ORDER BY quoteID DESC LIMIT $offset, $limit" );
	
?>
        <div class="wrap">
            <h1>All Quote List</h1>
            <table class="wp-list-table widefat fixed striped users">
                    <thead>
                        <tr>
                            <th>Quote No</th>
                            <th>Name</th>
                            <th>Email Address</th>  
                            <th>Submission Date</th>
                            <th colspan="2">View Detail</th> 
                        </tr>
                    </thead>
                    <?php foreach($entries as $entry){ ?> 
                    <tr>
                        <td><?php echo $entry->quoteID; ?></td>
                        <td><?php echo $entry->first_name.' '.$entry->surname; ?></td>
                        <td><?php echo $entry->email; ?></td>
                        <td><?php echo date('d-m-Y H:i:s',strtotime($entry->created_at)); ?></td>
                        <td><a href="admin.php?page=quickTest-quotes&action=view&quote_id=<?php echo $entry->quoteID; ?>">View Detail</a></td>
                        <td><a href="admin.php?page=quickTest-quotes&action=delete&quote_id=<?php echo $entry->quoteID; ?>">Delete Quote</a></td>
                    </tr>
                    <?php } ?>
             </table>
             <div class="tablenav bottom">
            <form name="" action="" method="post" enctype="multipart/form-data">
                <input type="submit" class="button" name="quote_csv" value="Download CSV" />
				<input type="submit" class="button" name="quote_priority" value="Download priority request quotes" />
                          
            </form> 
        </div>
            
<?php
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'text-domain' ),
			'next_text' => __( '&raquo;', 'text-domain' ),
			'total' => $num_of_pages,
			'current' => $pagenum
		) );
		
		if ( $page_links ) {
			echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
		}
		
		echo "</div>";
	}
}

function quickTest_quotes_view(){
	
}

function quickTest_quotes_activate(){
	
}

function quickTest_quotes_deactivate(){
	
}

register_activation_hook( __FILE__, 'quickTest_quotes_activate' );
register_deactivation_hook( __FILE__, 'quickTest_quotes_deactivate' );
?>
