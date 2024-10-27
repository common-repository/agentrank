<?php
/**
 *AgentRank List and Paginate Sale Transactions
 *Since Version 1.0
 */
// Number of records to show per page:
$display = 10;
//in this plugin
//note that start is start page
//starts from 0 index == page 1
//not the normal start record number.
if (isset($_GET['s'])) {
  $start = $_GET['s'];
} else {
  $start = 0;
}
global $agapi;
$xml = $agapi->view_all_sales($start, $display);
//if empty response print service unavailable message
if (empty($xml)) {
  $htm = "<div class='sidebar_error'>Service is Unavailable</div>";
  return $htm;
}
//check that $xml response is returned from api
if (!empty($xml)) {
  if ($xml->status['code'] == '400') {
    $error_message = $xml->status->messages->message;
    return "<p>$error_message</p><p>Please check your admin setting</p>";
  }
//parse total number of records from response
  $num_records = $xml->sales['total_results'];
// Determine how many pages there are.
  if (isset($_GET['np'])) { // Already been determined.
    $num_pages = $_GET['np'];
  } else { // Need to determine.
    // Calculate the number of pages.
    if ($num_records > $display) { // More than 1 page.
      $num_pages = ceil($num_records / $display); // use ceil to round up to nearest number.
    } else {
      $num_pages = 1;
    }
  } // End of np IF.
  $htm = "<div class='agentrank_sales'>";
  $htm .= "<h2 class='all_sales_title'>View All Sales</h2>";
  if ($status_code = "200") {
    $htm .= "<div class='agentrank-charts'>";
    foreach ($xml->charts->image as $image) {
      $image_title = $image['alt'];
      $image_url = $image['url'];
      $image_height = $image['height'];
      $image_width = $image['width'];
      $htm .= "<div class='agentrank-chart' style='width:33%;'><img src='$image_url' width='$image_width' height='$image_height' alt='$image_title'/></div>";
    }
    $htm .= "</div>";
    $htm .= "<br clear='both'/>";
    $htm .= "<ul>";
    foreach ($xml->sales->sale as $sale) {
      $id = $sale['id'];
      $title = $sale->title;
      $sale_url = get_bloginfo('url') . "/agentrank/sale?id=$id";
      $htm .= "<li><a href='$sale_url'>" . $title . "</a></li>";
    }
    $htm .= "</ul>";
  }
  //end if($status_code = "200")
  $q_url_structure = get_bloginfo('url') . "/agentrank/sales";
// Make the links to other pages, if necessary.
  if ($num_pages > 1) {
    $htm .= "<div class='agentrank-pagination'>";
    // Determine what page the script is on.
    $current_page = $start;
    // If it's not the first page, make a Previous button.
    if ($current_page != 0) {
      $htm .= "<span class=\"previous agentrank-page-numbers\" ><a href=\"$q_url_structure?s=" . ($start - 1) . "&np=" . $num_pages . "\">Previous</a></span> ";
    }
    // Make all the numbered pages.
    for ($i = 0; $i < $num_pages; $i++) {
      if ($i != $current_page) {
        $htm .= "<span class=\"agentrank-page-numbers\" ><a href=\"$q_url_structure?s=" . $i . "&np=" . $num_pages . "\">" . ($i + 1) . "</a></span> ";
      } else {
        $htm .= '<span class="agentrank-page-numbers current">';
        $htm .= ($i + 1);
        $htm .= '</span>';
        $htm .= ' ';
      }
    }
    // If it's not the last page, make a Next button.
    if (($current_page + 1) != $num_pages) {
      $htm .= "<span class=\"next agentrank-page-numbers\" ><a href=\"$q_url_structure?s=" . ($current_page + 1) . "&np=" . $num_pages . "\">Next</a></span>";
    }
    $htm .= "</div><!--end agentrank pagination-->";
  } // End of links section.
  $htm .= "</div>";
}
//end of if(!empty($xml))
return $htm;
?>