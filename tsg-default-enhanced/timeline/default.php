<?php
/** TSG-DEFAULT-ENHANCED Theme *******************************************************
 * theme        Default-Enhanced theme for com_vikevents v1.9
 * description  A simple responsive redesign and build replicating the orignal default
 * description  event layout utilising the installed framework default styling and layout
 * compat       Twitter Bootstrap v2.3.2, Twitter BootStrap v3.3.7
 * compat       YooTheme UIKit v2.26.4, JoomlaBamboo ZenGrid
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
  $timeline       = $this->timeline;
  $events         = $this->events;
  $vev_tn         = $this->vev_tn;

  $tf             = vikevents::getTimeFormat();
  $alldf          = vikevents::getDateFormat();
  $df             = $alldf == "%Y/%m/%d" ? 'm/d/Y' : 'd/m/Y';
  $dsep           = vikevents::getDateSeparator();
  $df             = str_replace('/', $dsep, $df);
  $days_in_month  = array();
  $now            = time();

  $weekdays       = array( 'VRSUN',
	                         'VRMON',
                      	   'VRTUE',
                      	   'VRWED',
                      	   'VRTHU',
                      	   'VRFRI',
                      	   'VRSAT'
                      	  );

  if(vikevents::loadJquery()) {
	  JHtml::_('jquery.framework', true, true);
	  JHtml::_('script', JURI::root().'components/com_vikevents/assets/jquery-1.11.1.min.js', false, true, false, false);
  }
  /** end standard vev global functions, params & variables **************************/

  /** custom tsg global functions, params & variables ********************************
   ** USER CONFIGURABLE OPTIONS - modify to suit your needs
   ***********************************************************************************/
   include_once('components/com_vikevents/themes/tsg-default-enhanced/themesetup.php');
  /** END USER CONFIGURABLE OPTIONS **************************************************/
?>


<!-- main events timeline page container -->
<div id="tsg-vevde-timeline">



  <?php if (count($timeline) > 0 && count($events) > 0): ?>

    <!-- event month & date selection -->
    <div class="vev-horiz-timeline tsg-vev-horiz-timeline">

      <!-- timeline months -->
    	<div class="vev-horiz-timeline-top">
      	<?php foreach ($timeline as $my => $days): // months with events ?>
          <?php
      		  $year  = substr($my, 0, 4);
      		  $mon   = substr($my, 4, (strlen($my) - 4));
      		  //The php function cal_days_in_month() is available only if PHP was compiled with support for calendar
      		  //$days_in_month[$my] = cal_days_in_month(CAL_GREGORIAN, intval($mon), $year);
      		  $days_in_month[$my] = (int)$mon == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (((int)$mon - 1) % 7 % 2 ? 30 : 31);
      		?>
      		<div class="vev-horiz-timeline-monyear tsg-vev-horiz-timeline-monyear" id="vevht_<?php echo $my; ?>">
      			<span class="vev-horiz-timeline-mon"><?php echo strtoupper(vikevents::sayMonth($mon)); ?></span>
      			<span class="vev-horiz-timeline-year"><?php echo $year; ?></span>
      		</div>
    	  <?php endforeach; // months with events ?>
    	</div> <!-- /timeline months -->

      <!-- timeline dates -->
    	<div class="vev-horiz-timeline-contbottom tsg-vev-horiz-timeline-contbottom">
    	  <?php foreach ($timeline as $my => $days): ?>
    		  <div class="vev-horiz-timeline-bottom" id="vevhtmy_<?php echo $my; ?>">
    		    <?php foreach ($days as $mday => $evs): ?>
    			    <div class="vev-horiz-timeline-mday tsg-vev-horiz-timeline-mday">
    				    <span class="vev-horiz-timeline-mday-sp" id="vevhtmday_<?php echo $my.$mday; ?>"><?php echo $mday; ?></span>
    			    </div>
    			  <?php endforeach; ?>
    		  </div>
    	  <?php endforeach; ?>
    	</div> <!-- /timeline dates -->

    </div> <!-- /event month & date selection -->


    <!-- main timeline -->
    <div class="vev-timeline-outer">
    <?php
      foreach ($timeline as $my => $days): //find all events by month
    	  $year = substr($my, 0, 4);
     	  $mon = substr($my, 4, (strlen($my) - 4));
    ?>

      	<!-- event month heading -->
      	<div class="vev-timeline-monyear tsg-vev-timeline-monyear" id="vevt_<?php echo $my; ?>">
      		<h3 class="vev-timeline-mon tsg-vev-timeline-mon">
      		  <?php echo vikevents::sayMonth($mon); ?>
      		</h3>
      		<span class="vev-timeline-year tsg-vev-timeline-year">
      		  <?php echo $year; ?>
      		</span>
      	</div> <!-- /event month heading -->


  	    <!-- timeline container -->
  	    <div class="vev-timeline-evcontainer tsg-vev-timeline-evcontainer">
  	    <?php
  	      $left_out  = false;

  	      foreach ($days as $mday => $evs): // grab dates and any events on each date
  		      $infowday  = getdate(mktime(0, 0, 0, $mon, $mday, $year));

  			    foreach ($evs as $k => $ev): // work out the number of days until the event start from today, or if it is in the past
              // convert epoch dates to formatted date
              $whatsToday      = date('d-m-Y G:i', $now);
              $eventStartDate  = date('d-m-Y G:i', $events[$ev]['tsinit']);
              $eventStartDate  = date('d-m-Y G:i', $infowday[0]);
              // create date_diff objects
              $viewToday       = date_create($whatsToday);
              $datetimeStart   = date_create($eventStartDate);
              // calculate how many days to go until the event starts
              $daysToGo        = date_diff($viewToday, $datetimeStart, false);
              $eventPast       = $daysToGo->invert; // 1 = event has gone past
            endforeach; // end work out daysToGo & if it is a past event
        ?>

            <!-- event date - row container -->
            <div class="row-fluid uk-grid uk-margin-remove zg-container tsg-timeline-container clearfix">

              <!-- event date column -->
              <div class="span2 col-md-2 uk-width-medium-1-6 zg-col zg-col-2 tsg-timeline-date-column clearfix">

                <div class="vev-timeline-ev-mday tsg-vev-timeline-ev-mday clearfix" id="vevtmday_<?php echo $my.$mday; ?>">
                  <div class="vev-timeline-ev-mday-inner tsg-vev-timeline-ev-mday-inner <?php if ($eventPast == '1'): echo 'pastEvent'; endif; ?> <?php echo $eventDateShape; ?>">

        					  <span class="vev-timeline-day-sp tsg-vev-timeline-day-sp">
        					    <?php echo $mday; ?>
        					  </span>

        				    <?php if (array_key_exists((int)$infowday['wday'], $weekdays)): ?>
        					    <span class="vev-timeline-weekday-sp tsg-vev-timeline-weekday-sp">
        					      <?php echo mb_substr(JText::_($weekdays[(int)$infowday['wday']]), 0, 3, 'UTF-8'); ?>
        					    </span>
        					  <?php endif; ?>

                  </div>
                </div>

              </div> <!-- /event date column -->

              <!-- event details column -->
              <div class="span10 col-md-10 uk-width-medium-5-6 zg-col zg-col-10 tsg-timline-detail-column clearfix">

    			      <?php foreach ($evs as $k => $ev): //rotate through events ?>

                  <!-- event detail container -->
                  <div class="span12 col-md-12 uk-width-1-1 zg-col zg-col-12 tsg-timeline-detail-container <?php if ($eventPast == '1'): echo 'pastEvent'; endif; ?> <?php echo $eventDetailShape; ?>">
                  <?php
                    /** per item - custom tsg params & variables **/
                    if (vikevents::stopReservations($events[$ev][params])): // stop-reservations : new in v1.9
                      $stopRes   = vikevents::stopReservations($events[$ev][params]);
                    else:
                      $stopRes   = '0';
                    endif;
                    if ($events[$ev][availnum] >= '1'): // event available tickets field is zero or empty : consider the event to be sold-out
                      $soldOut   = '0';
                    else:
                      $soldOut   = '1';
                    endif;
                  ?>

                    <?php if ($showStopSalesRibbon == '1' AND ($soldOut == '1' OR $stopRes == '1')): // event sold-out or reservations on-hold ?>

                      <!-- event sold-out or stop-reservations ribbon -->
                      <div class="ribbon <?php if ($soldOut == '1'): echo 'red'; elseif ($stopRes == '1'): echo 'silver'; else: echo $numDaysToGoColor; endif; ?>">
                        <span>
                        <?php
                          if ($soldOut == '1'): // event sold-out
                            echo JText::_('TSG_SOLD_OUT');
                          elseif ($stopRes == '1'): // stopReservations set
                            echo JText::_('TSG_ON_HOLD');
                          endif; // sold-out or end-reservations
                        ?>
                        </span>
                      </div> <!-- /event sold-out or stop-reservations ribbon -->

                    <?php elseif ($showDaysToGoRibbon == '1' AND ($soldOut != '1' AND $stopRes != '1') AND ($daysToGo->days <= $numDaysToGo) AND $eventPast == '0'): // show the daysToGo ribbon if not sold-out and stop-reservations not set and numDaysToGo is less than configured ?>

                      <!-- event daysToGo ribbon -->
                      <div class="ribbon <?php echo $numDaysToGoColor; ?>">
                        <span>
                          <?php
                            if ($daysToGo->days <= $numDaysToGo): // is $daysToGo within $numDaysToGo timeframe?

                              if ($daysToGo->days == '0'):
                                echo JText::_('TSG_TOMORROW');
                              elseif ($daysToGo->days == '1'):
                                echo $daysToGo->days .' '. JText::_('TSG_DAY_TOGO');
                              else:
                                echo $daysToGo->days .' '. JText::_('TSG_DAYS_TOGO');
                              endif;

                            endif; // numDaysToGo timeframe matched
                          ?>
                        </span>
                      </div> <!-- /event daysToGo ribbon -->

                    <?php elseif ($showFreeEventRibbon == '1' AND $events[$ev]['price'] == '0.00' AND ($soldOut != '1' OR $stopRes != '1')): // show free event ribbon if not sold-out and stop reservations not set or numDaysToGo is more than configured ?>

                      <div class="ribbon <?php echo $freeEventColor; ?>">
                        <span>
                          <?php echo JText::_('FREE EVENT'); ?>
                        </span>
                      </div> <!-- /free event -->

                    <?php elseif ($showAvailTickets == '1' AND $eventPast != '1' AND ($soldOut != '1' OR $stopRes != '1' OR $daysToGo->days > $numDaysToGo)): // show event available tickets if not sold-out and stop reservations not set or numDaysToGo is more than configured ?>

                      <!-- available tickets ribbon -->
                      <div class="ribbon <?php echo $availTicketsColor; ?>">
                        <span>
                          <?php
                            if ($soldOut == '0'):
                              echo $events[$ev][availnum].' '.JText::_('TSG_TICKETS_AVAILABLE');
                            else:
                              echo '0 '.JText::_('TSG_TICKETS_AVAILABLE');
                            endif;
                          ?>
                        </span>
                      </div> <!-- /available tickets ribbon  -->

                    <?php endif; ?>


                    <!-- event item -->
                    <div class="vev-timeline-ev-events-item">

                      <!-- event time -->
                      <div class="vev-timeline-ev-time tsg-vev-timeline-ev-time muted">
    					          <?php if(vikevents::showTime($events[$ev]['params'])): ?>
    							        <span style="margin:0 auto;"><?php echo date($tf, $events[$ev]['tsinit']); ?></span>
                        <?php else: ?>
    							        <span style="margin:0 auto;"><?php echo JText::_('TSG_TBA'); ?></span>
    					          <?php endif; ?>
    				          </div> <!-- /event time -->

                        <img class="timeline-moreinfo-image" src="components/com_vikevents/images/moreinf.png" />

                      <!-- event title & location -->
                      <div class="tsg-event-title-container">
                        <!-- event title -->
                        <h3 class="tsg-event-title tsg-event-title-truncate-2 " style="display:-webkit-box;">
                          <?php echo $events[$ev]['title']; ?>
                        </h3> <!-- / event title -->

                        <!-- event location -->
                        <div class="tsg-event-location tsg-text-truncate muted">
                          <?php
                            if ($events[$ev]['location']):
                              $saylocation = $events[$ev]['location'];
                            else:
                              $saylocation = JText::_('TSG_TBC');
                            endif;

                            echo $saylocation;
                          ?>
                        </div> <!-- /event location -->

                      </div> <!-- /event title & location -->


    						      <!-- event hidden details - small description, image & link -->
    						      <div class="vev-timeline-ev-item-hidden tsg-vev-timeline-ev-item-hidden">

                        <!-- responsive grid -->
                        <div class="row-fluid uk-grid zg-container clearfix">

                          <!-- show event image (or placehoder) -->
                          <div class="span4 col-md-4 uk-width-1-3 zg-col zg-col-4">

                            <!-- image and event link -->
                            <div class="vev-timeline-ev-image tsg-vev-timeline-ev-image">
                              <?php if ($linkToSoldOutEvent == '0' AND ($soldOut == '1' OR $eventPast == '1')): // don't link to event page ?>

                                <?php if ($events[$ev][img]): // show the 1st image if there is one, else use a placeholder image ?>
                                  <img class="<?php if ($soldOut == '1' OR $stopRes == '1' OR $eventPast == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/resources/<?php echo $events[$ev][img]; ?>" />
                                <?php else: // placeholder image ?>
                                  <img class="<?php if ($soldOut == '1' OR $stopRes == '1' OR $eventPast == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/themes/tsg-classic/images/event-placeholder.jpg" />
                                <?php endif; ?>

                              <?php else: // show link to event page ?>

                                <!-- link to event page -->
                                <a href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$events[$ev]['id']); ?>">
                                  <?php if ($events[$ev][img]): // show the 1st image if there is one, else use a placeholder image ?>
                                    <img class="<?php if ($soldOut == '1' OR $stopRes == '1' OR $eventPast == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/resources/<?php echo $events[$ev][img]; ?>" />
                                  <?php else: // placeholder image ?>
                                    <img class="<?php if ($soldOut == '1' OR $stopRes == '1' OR $eventPast == '1'): echo 'stopsales-'. $stopSalesImageFilter; endif; ?>" src="components/com_vikevents/themes/tsg-classic/images/event-placeholder.jpg" />
                                  <?php endif; ?>
                                </a>

                              <?php endif; ?>
                            </div> <!-- /image & event link -->

                          </div> <!-- /show event image (or placeholder -->

                          <!-- small description container -->
                          <div class="span8 col-md-8 uk-width-2-3 zg-col zg-col-8">
                            <?php if (strlen($events[$ev]['smalldescr']) > 0): ?>
                 							<div class="vev-timeline-ev-desc tsg-vev-timeline-ev-desc <?php if ($soldOut == '1'): echo 'muted'; endif; ?>">
                        				<?php echo $events[$ev]['smalldescr']; ?>
                 							</div>
                       			<?php endif; ?>
                          </div>

                        </div> <!-- responsive grid -->


                        <!-- link to event page or messaging -->
                        <?php if ($linkToSoldOutEvent == '0' AND $soldOut == '1'): // dont link to event page or messaging if sold-out and configured not to link ?>

                          <span class="alert alert-error alert-danger tsg-alert-soldout">
                            <?php echo JText::_('TSG_SOLD_OUT'); ?>
                          </span>

                        <?php elseif ($eventPast == '1'): // if event is an expired/previous event and not sold-out ?>

                          <span class="tsg-event-moreinfo">
                            <?php // show past-event information
                              if ($daysToGo->days == '0'):
                                echo JText::_('TSG_STARTED').' : '.JText::_('TSG_TODAY');
                              else:
                                echo JText::_('TSG_STARTED').' : '.$daysToGo->days.' '.JText::_('TSG_DAYS_AGO');
                              endif;
                            ?>
                          </span>

                        <?php else: // link to the event page ?>

                          <a class="tsg-event-moreinfo" href="<?php echo JRoute::_('index.php?option=com_vikevents&view=event&itid='.$events[$ev]['id']); ?>">
                            <?php echo JText::_('VEVMOREINFOR'); ?>&nbsp;&rang;&rang;
                          </a>

                        <?php endif; // end link to event page or messaging ?>

    						      </div> <!-- /event hidden details - small description, image & link -->

                    </div> <!-- /event item -->


                  </div> <!-- event detail container -->

                <?php endforeach; // end rotate through events ?>

  			      </div> <!-- /event details column -->

  		      </div> <!-- /event date - row container -->


  		    <?php endforeach; // end grabbing dates and any events on each date?>

  	    </div> <!-- /timeline container -->

  	  <?php endforeach; // end find all events by month ?>

    </div> <!-- /main timeline -->


    <script type="text/javascript">
      jQuery.noConflict();
      var days_in_month = <?php echo json_encode($days_in_month); ?>;
      jQuery(document).ready(function(){
      	var htwidth = jQuery('.vev-horiz-timeline-contbottom').width();
      	if(!jQuery.isEmptyObject(days_in_month)) {
      		jQuery('.vev-horiz-timeline-bottom').each(function(){
      			var monyearattr = jQuery(this).attr('id');
      			var monyear = monyearattr.split('_');
      			var d_in_m = 0;
      			if(days_in_month.hasOwnProperty(monyear[1])) {
      				d_in_m = parseInt(days_in_month[monyear[1]]);
      			}
      			var htdayswidth = 0;
      			var htonedaswidth = 0;
      			var htdayscount = 0;
      			jQuery(this).find('div.vev-horiz-timeline-mday').each(function(){
      				htdayswidth += jQuery(this).outerWidth(true);
      				htonedaswidth = jQuery(this).outerWidth(true);
      				htdayscount++;
      			});
      			if(d_in_m > 0 && (htonedaswidth * d_in_m) <= htwidth) {
      				jQuery(this).find('div.vev-horiz-timeline-mday').each(function(){
      					var d_of_m = parseInt(jQuery(this).find('span').text());
      					var marginleft = (htwidth / d_in_m * d_of_m) - htonedaswidth;
      					jQuery(this).css({'position': 'absolute', 'left': marginleft});
      				});
      			}
      		});
      	}
      	jQuery('.vev-horiz-timeline-bottom:first').fadeIn(400, function() {
      		jQuery('.vev-horiz-timeline-monyear:first').addClass('vev-ht-cur-mon');
      	});
      	jQuery('.vev-horiz-timeline-monyear').click(function(){
      		var monyear = jQuery(this).attr('id');
      		var monyear_timeline = monyear.split('_');
      		if(jQuery(this).hasClass('vev-ht-cur-mon')) {
      			jQuery('html,body').animate({ scrollTop: (jQuery('#vevt_'+monyear_timeline[1]).offset().top) }, { duration: 'slow' });
      		}else {
      			jQuery('.vev-horiz-timeline-bottom').hide();
      			jQuery('.vev-horiz-timeline-monyear').removeClass('vev-ht-cur-mon');
      			jQuery(this).addClass('vev-ht-cur-mon');
      			jQuery('#vevhtmy_'+monyear_timeline[1]).fadeIn();
      		}
      	});
      	jQuery('.vev-horiz-timeline-mday-sp').click(function(){
      		var monyear = jQuery(this).attr('id');
      		var monyear_timeline = monyear.split('_');
      		jQuery('html,body').animate({ scrollTop: (jQuery('#vevtmday_'+monyear_timeline[1]).offset().top) }, { duration: 'slow' });
      	});
      	jQuery('.vev-timeline-ev-events-item').click(function(){
      		var hidelem = jQuery(this).find(".vev-timeline-ev-item-hidden");
      		if(hidelem.length) {
      			if(hidelem.is(":visible")) {
      				hidelem.slideUp();
      			}else {
      				hidelem.slideDown();
      			}
      		}
      	});
      });
    </script>


  <?php else: // no events to shows ?>


    <h3 class="tsg-event-title" style="text-align: center;">
      <?php echo JText::_('TSG_NOEVENTS'); ?>
    </h3>


  <?php endif; // end count & show events ?>



</div> <!-- /main tsg container -->