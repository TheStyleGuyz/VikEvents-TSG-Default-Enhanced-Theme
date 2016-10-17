<?php
/** TSG-DEFAULT-ENHANCED Theme *******************************************************
 * theme        Default-Enhanced theme for com_vikevents v1.9
 * description  A simple responsive redesign and build replicating the orignal default
 * description  event layout utilising the installed framework default styling and layout
 * compat       Twitter Bootstrap v2.3.2, Twitter BootStrap v3.3.7
 * compat       YooTheme UIKit v2.26.4, UIKit v3 (Beta), JoomlaBamboo ZenGrid
 * author       RussW (TheStyleGuyz, a division of hotmango, web and print. Australia.)
 * copyright    Copyright (c) 2016, The Style Guyz. All rights reserved.
 * @license     Dual GNU/GPL2 & TSG:PL1 - Proprietary License
 * @license     TSG:PLv1 - TSG custom functions & linked Cascading Style Sheets
 * @license     TSG:PLv1 - http://www.thestyleguyz.com/licenses/
 * website      http://www.thestyleguyz.com/ (a division of http://www.hotmango.me)
 * github       https://github.com/TheStyleGuyzTeam
 * support      http://www.thestyleguyz.com/support/
 *************************************************************************************/

/** e4j VikEvents ********************************************************************
 * attribution  The VikEvents component and some functions included in this TSG theme
 * attribution  are copyright and the intellectual property of e4j (com_vikevents)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * copyright    Copyright (C) 2015 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * website      http://www.extensionsforjoomla.com
 *************************************************************************************/

defined('_JEXEC') OR die('Restricted Area');

  /** standard vev global functions, params & variables ******************************/
  if (vikevents::loadJquery()):
    JHtml::_('jquery.framework', true, true);
    JHtml::_('script', JURI::root().'components/com_vikevents/assets/jquery-1.11.1.min.js', false, true, false, false);
  endif;

  $rows          = $this->rows;
  $pagelinks     = $this->pagelinks;
  $tf            = vikevents::getTimeFormat();
  $showlocmap    = true;
  $currencysymb  = vikevents::getCurrencySymb();
  $now           = time();
  /** end standard vev global functions, params & variables **************************/

  /** custom tsg global functions, params & variables ********************************
   ** USER CONFIGURABLE OPTIONS - modify to suit your needs
   ***********************************************************************************/
   include_once('components/com_vikevents/themes/tsg-default-enhanced/themesetup.php');
  /** END USER CONFIGURABLE OPTIONS **************************************************/

   // UIKITFIX(@russw) : is it a YOOTheme Warp(UIKit2) or Pro(UIKit3) template
   $app           = JFactory::getApplication();
   $isYOOWarp     = JPATH_SITE.'/templates/'.$app->getTemplate().'/warp.php';
   $isYOOPro      = JPATH_SITE.'/templates/'.$app->getTemplate().'/templates/';
   if (file_exists($isYOOWarp)):
     $isYOOTheme  = 'YOOWarp';
     $rowLayout   = '';
   elseif (file_exists($isYOOPro)):
     $isYOOTheme  = 'YOOPro';
     $rowLayout   = '';
   else:
     $isYOOTheme  = '0';
     $rowLayout   = 'row-fluid';
   endif;

  function cmp($rows, $b) { // sort the array by-date, even if the event date(s) have been edited/modified later
    if ($rows['tsinit'] == $b['tsinit']):
      return 0;
    endif;
    return ($rows['tsinit'] < $b['tsinit']) ? -1 : 1;
  }
  $rows  = $this->rows;
  usort($rows, "cmp");
  $countEvents  = '1';
  /** end custom tsg global functions, params & variables ****************************/
?>


<!-- main events page container -->
<div id="tsg-vevde-list">



  <?php if (@is_array($rows)): ?>

    <?php foreach ($rows as $r): ?>

    <?php /** standard vev params & variables **/
  	  if (!empty($r['loclat']) && !empty($r['loclng'])):
  		  $showlocmap  = true;
  		  $document->addStyleSheet(JURI::root().'components/com_vikevents/assets/jquery.fancybox.css');
  		  JHtml::_('script', JURI::root().'components/com_vikevents/assets/jquery.fancybox.js', false, true, false, false);

  		  $navdecl = '
jQuery.noConflict();
jQuery(document).ready(function() {
jQuery(".vevmodal").fancybox({
"helpers": {
	"overlay": {
		"locked": false
 	}
},
"width": "75%",
"height": "75%",
"autoScale": false,
"transitionIn": "none",
"transitionOut": "none",
"padding": 0,
"type": "iframe"
});
});';
 		    $document->addScriptDeclaration($navdecl);
 	      break;
 	    endif;
      //
      $alldf       = vikevents :: getDateFormat();
      $df          = $alldf == "%Y/%m/%d" ? 'm/d/Y' : 'd/m/Y';
      $dsep        = vikevents::getDateSeparator();
      $df          = str_replace('/', $dsep, $df);
    ?>
    <?php endforeach; ?>


      <?php foreach ($rows as $r): // cycle through the events ?>

        <?php
          /** per item - custom tsg params & variables **/
          if (vikevents::stopReservations($r[params])): // stop-reservations : new in v1.9
            $stopRes   = vikevents::stopReservations($r[params]);
          else:
            $stopRes   = '0';
          endif;
          if ($r[availnum] >= '1'): // event available tickets field is zero or empty : consider the event to be sold-out
            $soldOut   = '0';
          else:
            $soldOut   = '1';
          endif;
          /** per item - standard vev params & variables **/
      		$showtime    = vikevents::showTime($r[params]);
      	  $forcegview  = vikevents::forceEventGroupView($r[params]);
      	  $rdf         = $showtime ? $df.' '.$tf : $df;
        ?>
<?php //'<pre>'. var_dump($r) .'</pre>'; ?>
        <!-- event row, container -->
        <div class="<?php $rowLayout; ?> uk-grid uk-margin-remove zg-container tsg-event-container clearfix">
          <!-- single event flex container -->
          <div class="span12 uk-width-1-1 uk-width-1-1 tsg-event-row-container <?php echo $eventDetailShape; ?>">

            <div class="span2 col-md-2 uk-width-medium-1-6 uk-width-1-6@m uk-margin-remove zg-col zg-col-2 tsg-event-datetime-column clearfix">

              <!-- event date & time -->
              <div class="tsg-event-datetime-container">

                <?php
                  // work out the number of days until the event from today
                  // convert epoch dates to formatted date
                  $whatsToday      = date('d-m-Y G:i', $now);
                  $eventStartDate  = date('d-m-Y G:i', $r['tsinit']);
                  // create date_diff objects
                  $viewToday       = date_create($whatsToday);
                  $datetimeStart   = date_create($eventStartDate);
                  // calculate how many days to go until the event starts
                  $daysToGo        = date_diff($viewToday, $datetimeStart);
                ?>

              <?php if ($showStopSalesRibbon == '1' AND ($soldOut == '1' OR $stopRes == '1')): // event sold-out or reservations on-hold ?>

                <div class="ribbon <?php if ($soldOut == '1'): echo 'red'; elseif ($stopRes == '1'): echo 'silver'; else: echo $numDaysToGoColor; endif; ?>">
                  <span>
                    <?php if ($soldOut == '1'): // event sold-out ?>
                      <?php echo JText::_('TSG_SOLD_OUT'); ?>
                    <?php elseif ($stopRes == '1'): ?>
                      <?php echo JText::_('TSG_ON_HOLD'); // event stop-reservations ?>
                    <?php endif; // sold-out or end-reservations ?>
                  </span>
                </div>

              <?php elseif ($showDaysToGoRibbon == '1' AND ($soldOut != '1' AND $stopRes != '1') AND $daysToGo->days <= $numDaysToGo): // show the daysToGo ribbon if not sold-out and stop-reservations not set and numDaysToGo is less than configured ?>

                <div class="ribbon <?php echo $numDaysToGoColor; ?>">
                  <span>
                    <?php
                      if ($daysToGo->days <= $numDaysToGo): // is $daysToGo within $numDaysToGo timeframe?

                        if ($daysToGo->days == '1'):
                          echo JText::_('TSG_TOMORROW');
                        elseif ($daysToGo->days == '0'):
                          echo JText::_('TSG_TODAY');
                        else:
                          echo $daysToGo->days .' '. JText::_('TSG_DAYS_TOGO');
                        endif;

                      endif; // numDaysToGo timeframe matched
                    ?>
                  </span>
                </div> <!-- /days to go notice -->

              <?php elseif ($showFreeEventRibbon == '1' AND $r['price'] == '0.00' AND ($soldOut != '1' OR $stopRes != '1')): // show free event ribbon if not sold-out and stop reservations not set or numDaysToGo is more than configured ?>

                <div class="ribbon <?php echo $freeEventColor; ?>">
                  <span>
                    <?php echo JText::_('FREE EVENT'); ?>
                  </span>
                </div> <!-- /free event -->

              <?php elseif ($showAvailTickets == '1' AND ($soldOut != '1' OR $stopRes != '1' OR $daysToGo->days > $numDaysToGo)): // show event available tickets if not sold-out and stop reservations not set or numDaysToGo is more than configured ?>

                <div class="ribbon <?php echo $availTicketsColor; ?>">
                  <span>
                    <?php
                      if ($r[availnum] >= '1'):
                        echo $r[availnum].' '.JText::_('TSG_TICKETS_AVAILABLE');
                      else:
                        echo '0 '.JText::_('TSG_TICKETS_AVAILABLE');
                      endif;
                    ?>
                  </span>
                </div> <!-- /number of available tickets -->

              <?php endif; ?>


                <div class="tsg-event-date<?php if (!$showtime): echo '-only'; endif; ?>">
                  <?php
                		//if dayselection and init after now check next date
                		if ($r['tsinit'] < $now && $r['dayselection'] == 1) {
                			$excludets = array();
                			if (!empty($r['excludedays'])) {
                				$exdays = explode(";", $r['excludedays']);
                				foreach($exdays as $exd) {
                					if (!empty($exd)) {
                						$parts = explode("-", $exd);
                						$excludets[] = strtotime(((int)$parts[0] < 10 ? "0".$parts[0] : $parts[0])."/".((int)$parts[1] < 10 ? "0".$parts[1] : $parts[1])."/".$parts[2]);
                					}
                				}
                			}
                			$baseinit = getdate($r['tsinit']);
                			$basets = strtotime(($baseinit['mon'] < 10 ? "0".$baseinit['mon'] : $baseinit['mon'])."/".($baseinit['mday'] < 10 ? "0".$baseinit['mday'] : $baseinit['mday'])."/".$baseinit['year']);
                			$addseconds = $r['tsinit'] - $basets;
                			$is_dst = date('I', $r['tsinit']);
                			for($hd = $basets; $hd <= $r['tsend']; $hd+=86400) {
                				$is_now_dst = date('I', $hd);
                				if ($is_dst != $is_now_dst) {
                					//Daylight Saving Time has changed, check how
                					if ((int)$is_dst == 1) {
                						$hd += 3600;
                					}else {
                						$hd -= 3600;
                					}
                					$is_dst = $is_now_dst;
                				}
                				if (($hd + $addseconds) >= $now && !in_array($hd, $excludets)) {
                					$startinfo = getdate(($hd + $addseconds));
                					break;
                				}
                			}
                		}else {
                			$startinfo = getdate($r['tsinit']);
                		}
                		?>
                  <span>
                    <?php echo $startinfo['mday'].' '.mb_substr(vikevents::sayMonth($startinfo['mon']), 0, 3, 'UTF-8'); ?>
                  </span>
                </div>

                <?php if ($showtime): ?>
                  <div class="tsg-event-time">
                    <span>
                      <?php echo JText::sprintf('VEVTIMEHM', date($tf, $startinfo[0])); ?>
                    </span>
                  </div>
                <?php endif; ?>

              </div> <!-- /event datetime container -->

            </div> <!-- span2 column -->


            <div class="span4 col-md-4 uk-width-medium-2-6 uk-width-1-3@m zg-col zg-col-4 tsg-event-image-column clearfix">

              <!-- event image container -->
              <div class="tsg-event-image-container">
                <?php if ($linkToSoldOutEvent == '0' AND $soldOut == '1'): ?>

                  <?php if ($r[img]): // show the 1st image if there is one, else try the 2nd image, else use a placeholder image ?>
                    <img class="<?php if ($soldOut == '1' OR $stopRes == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/resources/<?php echo $r[img]; ?>" />
                  <?php else: ?>
                    <img class="<?php if ($soldOut == '1' OR $stopRes == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/themes/tsg-classic/images/event-placeholder.jpg" />
                  <?php endif; ?>

                <?php else: ?>

                  <a href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$r['id'].($forcegview ? '&gview=1' : '')); ?>">
                    <?php if ($r[img]): // show the 1st image if there is one, else try the 2nd image, else use a placeholder image ?>
                      <img class="<?php if ($soldOut == '1' OR $stopRes == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/resources/<?php echo $r[img]; ?>" />
                    <?php else: ?>
                      <img class="<?php if ($soldOut == '1' OR $stopRes == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/themes/tsg-classic/images/event-placeholder.jpg" />
                    <?php endif; ?>
                  </a>

                <?php endif; ?>
              </div> <!-- /event image container -->

            </div> <!-- /span4 column -->


            <div class="span6 col-md-6 uk-width-medium-1-2 uk-width-1-2@m uk-margin-remove zg-col zg-col-6 tsg-event-detail-column clearfix">

              <!-- event detail container -->
              <div class="tsg-event-detail-container <?php if ($soldOut == '1' OR $stopRes == '1'): echo 'muted'; endif; ?>">

                <h3 class="tsg-event-title tsg-text-truncate">
                  <?php if ($linkToSoldOutEvent == '0' AND $soldOut == '1'): ?>
                    <?php echo $r[title]; ?>
                  <?php else: ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$r['id'].($forcegview ? '&gview=1' : '')); ?>">
                      <?php echo $r[title]; ?>
                    </a>
                  <?php endif; ?>
                </h3>

                <div class="tsg-event-location tsg-text-truncate">
                  <?php
                    if (!empty($r[location])):
   			              if ($showlocmap == true AND (!empty($r['loclat']) AND !empty($r['loclng']))):
      				          $saylocation = '<a href="'.JRoute::_('index.php?option=com_vikevents&view=locationmap&itid='.$r['id'].'&tmpl=component').'" class="vevmodal" target="_blank" id="vevopenlocmap">'.$r['location'].'</a>';
                      else:
                        $saylocation = $r[location];
                      endif;
                    else:
                      $saylocation = JText::_('TSG_TBC');
                    endif;

                    echo $saylocation;
                  ?>
                </div>

                <p class="tsg-event-smalldesc tsg-smalldesc-truncate-4">
                  <?php echo $r[smalldescr]; ?>
                </p>

                <?php if ($linkToSoldOutEvent == '0' AND $soldOut == '1'): ?>
                  <span class="alert alert-error alert-danger tsg-alert-soldout">
                    <?php echo JText::_('TSG_SOLD_OUT'); ?>
                  </span>
                <?php else: ?>
                  <a class="tsg-event-moreinfo" href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$r['id'].($forcegview ? '&gview=1' : '')); ?>">
                    <?php echo JText::_('VEVMOREINFOR'); ?>&nbsp;&rang;&rang;
                  </a>
                <?php endif; ?>

                <?php if ($linkToSoldOutEvent == '1' AND ($soldOut != '1' AND $stopRes != '1')): ?>
                  <a class="tsg-event-moreinfo" href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$r['id'].($forcegview ? '&gview=1' : '')); ?>">
                    <?php echo JText::_('VEVMOREINFOR'); ?>&nbsp;&rang;&rang;
                  </a>
                <?php endif; ?>

              </div> <!-- /event detail container -->

            </div> <!-- /span6 column -->

          </div> <!-- /single event flex container -->
        </div> <!-- /event row, container -->


        <?php
          // if enabled, display the module(s) located in the "inlineEventAdvert" position
          if ($showModule == '1'):
            if ($countEvents % $showModuleEvery == '0'):

              echo '<div class="inlineEventAdvert">';
                $insertModule = JModuleHelper::getModules('inlineEventAdvert');
                if (@$insertModule):
                  $renderer  = $document->loadRenderer('modules');
                  $options   = array('style'=>'raw');

                  echo $renderer->render('inlineEventAdvert', $options, null);
                endif;

                if ($showModule == '1' AND !$insertModule): // showModules is enabled but no published modules to display
                  echo '<p class="alert alert-error alert-danger text-left"><strong>TSG Theme Message:</strong><br />"showModules" is ENABLED, but there are NO PUBLISHED modules in the "inlineEventAdvert" position to display. Either disable showModules in the theme user options or publish a module to the inlineEventAdvert position.</p>';
                endif;
              echo '</div>';

            endif;
          endif;
        ?>

      <?php $countEvents++; ?>
      <?php endforeach; // end event cycle ?>


    	<div class="vevpagination">
    	  <?php echo $pagelinks; ?>
    	</div>


  <?php else: ?>

    <h3 class="tsg-event-title" style="text-align: center;">
      <?php echo JText::_('TSG_NOEVENTS'); ?>
    </h3>

  <?php endif; // end is_array ?>



</div> <!-- /main events page container -->