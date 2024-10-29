<?php
/*
Plugin Name: AdWol Werbung
Plugin Script: adwol-werbung.php
Description: Blog Monetarisierung - Banner, Widgets, InPost, RecommendAds.
Version: 1.3
License: GPL
Author: AdWol
Author URI: https://adwol.com
Plugin URI: https://adwol.com/wordpress-plugin

=== RELEASE NOTES ===
2014-01-16 - v1.0 - first version
2015-09-11 - v1.1 - responsive size added
2016-03-31 - v1.2 - responsive size added
2019-12-23 - v1.3 - small bugfixes

=== LICENSE ===
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/

class AdWolWidget1 extends WP_Widget {
	function AdWolWidget1() {
		parent::__construct( false, 'AdWol Werbung Responsive' );
	}

	function widget( $args, $instance ) {
		echo '<script type="text/javascript" src="https://adwol.com/a/ad?p='.get_option('wp_adwol_publisher').'&s=r"></script>';
	}
}
function myplugin_register_widgets_adwol() {
	register_widget( 'AdWolWidget1' );
}
add_action( 'widgets_init', 'myplugin_register_widgets_adwol' );

function inject_ad_text_after_n_chars_adwol($content) {
  // only if post is longer than 500 characters
  $enable_length = 500;
  // insert after the first </p> after 500 characters
  $after_character = 500;
  if (is_single() && strlen($content) > $enable_length) {
    $before_content = substr($content, 0, $after_character);
    $after_content = substr($content, $after_character);
    $after_content = explode('</p>', $after_content);
    $text = '<script type="text/javascript" src="https://adwol.com/a/ad?p='.get_option('wp_adwol_publisher').'&s=r"></script>';
    array_splice($after_content, 1, 0, $text);
    $after_content = implode('</p>', $after_content);
    return $before_content . $after_content;
  }
  else {
    return $content;
  }
}
if (get_option('wp_adwol_bei')!='ein') {
add_filter('the_content', 'inject_ad_text_after_n_chars_adwol');
}


function addRelAdWol($content) {
	if ( is_single ( ) ) {
		
		global $post;
		global $adwolsave;
		
		if ($adwolsave=='') {
		
		$tags = wp_get_post_tags($post->ID);
		$i = 0;
		
		$titleadl=get_the_title();
		
		if ($tags) { 
		
		foreach ($tags as $tagsin) {
		$tagadlist.=$tagsin->name.',';
		}
		
			$tag_ids = array();			
			foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
				$related = array(
					'tag__in' => $tag_ids,
					'post__not_in' => array($post->ID),
					'post_type' => 'post',
					'posts_per_page'=>3,
					'ignore_sticky_posts'=>1
				);

			$adwrel_query = new WP_Query($related);


			if ( $adwrel_query ->have_posts() ) {
				while ( $adwrel_query->have_posts() ) {
					$adwrel_query->the_post();
					$conad=$adwrel_query->posts[$i]->post_content;
					$excad=substr(strip_tags($conad),0,100)."...";
					preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $conad, $imageadw);
					$imsrad='';
					$imsrad='https://images.weserv.nl/?w=75&h=75&t=square&url='.urlencode(str_replace('http://','',str_replace('https://','',$imageadw['src'])));
					if ($imsrad=='') {
					$imsrad='https://images.weserv.nl/?w=75&h=75&t=square&url=adwol.com/norel.jpg';
					}
					
					if( $i < 1){
					    //ADWOL HINWEIS DARF NICHT ENTFERNT ODER GEÄNDERT WERDEN!
						//IT IS NOT ALLOWED TO CHANGE OR REMOVE THE ADWOL BRANDING!
						$html .= '<h4>Das könnte interessant sein <a target="_blank" href="https://adwol.com/?rel=1" title="AdWol Online Werbung"><img src="https://adwol.com/ads-by.png" alt="Powered by AdWol Online Werbung"></a></h4>';
						$html .= '<table>';
					}
					
					if( $i == 1){						
						$html .= '<script type="text/javascript" src="https://adwol.com/a/f?p='.get_option('wp_adwol_publisher').'&s=rel&k='.urlencode($tagadlist).'&t='.urlencode($titleadl).'"></script>';
					}
					
					
					
					$html .= '<tr style="cursor:pointer;" onclick="window.location=\''.get_the_permalink().'\';">
							  <td style="vertical-align:top;min-width:75px;"><img onerror="imgADError(this);" src="'.$imsrad.'"></td>
							  <td style="vertical-align:top;">				          
					          <b>'.get_the_title().'</b><p style="color:#767676;">
							  '.$excad.'
							  </p>
							  </td>
					          </tr>';
					$i++;
				}
				$html .= '</table>
				<script>
function imgADError(image){
    image.onerror = "";
    image.src = "https://images.weserv.nl/?w=75&h=75&t=square&url=adwol.com/norel.jpg";
    return true;
}
</script>
				';
			} 
			wp_reset_query();
		} else {
			
			$categories = get_the_category();
			if ($categories){
				$category = $categories[0];
				$cat_ID = $category->cat_ID;
			} else {
				$cat_ID = '';
			}

			$related = array(
					'cat' => $cat_ID,
					'post__not_in' => array($post->ID),
					'post_type' => 'post',
					'posts_per_page'=>3,
					'ignore_sticky_posts'=>1
				);

			$adwrel_query2 = new WP_Query($related);

			if ( $adwrel_query2->have_posts() ) {
				while ( $adwrel_query2->have_posts() ) {
					
					$adwrel_query2->the_post();
					$conad=$adwrel_query2->posts[$i]->post_content;
					$excad=substr(strip_tags($conad),0,100)."...";
					preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $conad, $imageadw);
					$imsrad='';
					$imsrad='https://images.weserv.nl/?w=75&h=75&t=square&url='.urlencode(str_replace('http://','',str_replace('https://','',$imageadw['src'])));
					if ($imsrad=='') {
					$imsrad='https://images.weserv.nl/?w=75&h=75&t=square&url=adwol.com/norel.jpg';
					}
					
					if( $i < 1){
						//ADWOL HINWEIS DARF NICHT ENTFERNT ODER GEÄNDERT WERDEN!
						//IT IS NOT ALLOWED TO CHANGE OR REMOVE THE ADWOL BRANDING!
						$html .= '<h4>Das könnte interessant sein <a target="_blank" href="https://adwol.com/?rel=1" title="AdWol Online Werbung"><img src="https://adwol.com/ads-by.png" alt="Powered by AdWol Online Werbung"></a></h4>';
						$html .= '<table>';
					}
					
					
					if( $i == 1){						
						$html .= '<script type="text/javascript" src="https://adwol.com/a/f?p='.get_option('wp_adwol_publisher').'&s=rel&k='.urlencode($tagadlist).'&t='.urlencode($titleadl).'"></script>';
					}
											
					
					$html .= '<tr style="cursor:pointer;" onclick="window.location=\''.get_the_permalink().'\';">
							  <td style="vertical-align:top;min-width:75px;"><img onerror="imgADError(this);" src="'.$imsrad.'"></td>
							  <td style="vertical-align:top;">				          
					          <b>'.get_the_title().'</b><p style="color:#767676;">
							  '.$excad.'
							  </p>
							  </td>
					          </tr>';
					$i++;
				}
				$html .= '</table>
<script>
function imgADError(image){
    image.onerror = "";
    image.src = "https://images.weserv.nl/?w=75&h=75&t=square&url=adwol.com/norel.jpg";
    return true;
}
</script>';
			} 
			wp_reset_query();

		}
		
		$adwolsave=$html;

		}
		
		

	$content = $content . $adwolsave;
	return $content;
		
	
	} else {
	return $content;
}
}

if (get_option('wp_adwol_rel')!='ein') {
add_action('the_content', 'addRelAdWol');
}


add_action('admin_menu', 'my_plugin_menu_adwol_werbung');

function my_plugin_menu_adwol_werbung() {
	add_options_page('Preferences', 'AdWol Werbung', 'manage_options', 'adwolwerbung', 'my_plugin_options_adwol_werbung');
}

function my_plugin_options_adwol_werbung() {
if (!current_user_can('manage_options'))
{
wp_die( __('You do not have sufficient permissions to access this page.') );
}
	
if( $_POST['publisher'] != '' ) {
update_option('wp_adwol_publisher',$_POST['publisher']);
$updatead=1;
}

if( $_POST['adwolbei'] != '' ) {
update_option('wp_adwol_bei',$_POST['adwolbei']);
$updatead=1;
}

if( $_POST['adwolrel'] != '' ) {
update_option('wp_adwol_rel',$_POST['adwolrel']);
$updatead=1;
}

if ($updatead==1) { ?>
<div class="updated"><p><strong><?php _e('Einstellungen gespeichert.', 'menu-start' ); ?></strong></p></div>
<?php } ?>
<div class="wrap">
<h2>AdWol Werbung</h2>
Anleitung zur Monetarisierung der Webseite.
<ol>
<li>Registrieren Sie sich kostenlos unter <a href="https://adwol.com/register" target="_blank">adwol.com/register</a>.</li>
<li>Sie finden Ihre Publisher-ID im Benutzerbereich von AdWol: Publisher > Website-Banner > WordPress Plugin.</li>
<li>Gebe Sie die Publisher-ID auf dieser Seite ein.</li>
<li>Fügen Sie das Werbewidget von AdWol zu Ihrer Seite hinzu. Aktivieren Sie die "Werbung in Beiträgen" und "Ähnliche Beiträge inkl. Werbung".</li>
</ol>

<form name="form1" method="post" action="">
Publisher-ID: <input type="text" name="publisher" value="<?php echo get_option('wp_adwol_publisher'); ?>"><br><br>
Werbung in Beiträgen: 
<input type="radio" name="adwolbei" value="aus" <?php if (get_option('wp_adwol_bei')!='ein') {echo 'checked="checked"';} ?>>ja <input type="radio" name="adwolbei" value="ein" <?php if (get_option('wp_adwol_bei')=='ein') {echo 'checked="checked"';} ?>>nein

<br><br>
Ähnliche Beiträge vorschlagen mit Werbung: 
<input type="radio" name="adwolrel" value="aus" <?php if (get_option('wp_adwol_rel')!='ein') {echo 'checked="checked"';} ?>>ja <input type="radio" name="adwolrel" value="ein" <?php if (get_option('wp_adwol_rel')=='ein') {echo 'checked="checked"';} ?>>nein

<hr />

<p style="float:left;" class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Einstellungen speichern') ?>" />
</p>

<p style="float:right;">
<a target="_blank" title="Online Werbung" href="https://adwol.com"><img src="https://adwol.com/adwol_logo_re.png" alt="AdWol" /></a>
</p>

</form>
<div style="clear:both;"></div>
<br><br>
<small>
Um die Funktion "Ähnliche Beiträge" nutzen zu können, müssen Sie entsprechende Tags und Kategorien anlegen.
<br>
Wir schließen Gewährleistung und Haftung aus. Nutzung dieses Plugins erfolgt auf eigene Gefahr. Bitte überprüfen Sie selbst den Quellcode.
</small>
</div>

<?php
}
?>