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

 defined('_JEXEC') OR die('Restricted Area');

  /** custom tsg-theme language file *************************************************/
   $language  = JFactory::getLanguage();
   $language->load('com_vikevents_custom', JPATH_SITE.'/components/com_vikevents/themes/tsg-default-enhanced');

  /** custom tsg-theme css ***********************************************************/
   $document  = JFactory::getDocument();
   $document->addStyleSheet( JURI::root().'components/com_vikevents/themes/tsg-default-enhanced/css/tsg-default-enhanced.css' );


  /** custom tsg global functions, params & variables ********************************
   ** USER CONFIGURABLE OPTIONS - modify to suit your needs
   ** USER OPTIONS NOTES:
   ** A) eventDateShape option effects *ONLY* the Timeline view
   ** B) eventDetailShape option effects *BOTH* the List & Timeline views
   ** C) Corner Ribbon display cascades (if enabled) until matched in the following order:
   **   1) if Sold Out (colour: red)
   **   2) if Stop Reservation (OnHold) set (colour: silver)
   **   3) number of Days To Go (upto configured daysToGo limit)
   **   4) is a Free Event (colour: configurable)
   **   5) number of Available Tickets (colour: configurable)
   ** D) Joomla! position to be used for (published) module display : inlineEventAdvert
   ***********************************************************************************/

   // event date & detail container shape
   $eventDateShape        = 'rounded';      // change the *TIMELINE ONLY* shape of the events date container (valid options = square, rounded, radius)
   $eventDetailShape      = 'square';       // change the LIST & TIMELINE shape of the events detail container (valid options = square, rounded, radius)

   // sold-out and stop-reservation events options
   $showStopSalesRibbon     = '1';          // { show the "Sold Out" (red) & "On Hold" (silver) ribbon message (1 = yes, 0 = no)
   $stopSalesImageFilter    = 'greyscale';  // { filter the event image if stopReservations is set or the is event sold-out; image filter type (greyscale, blur, opacity, sepia or 'none' = disabled)
   // sold-out event detail options
   $linkToSoldOutEvent      = '0';          // enable links to the main event page if sold-out (all other events are always linked) (1 = yes, 0 = no)

   // free event options
   $showFreeEventRibbon     = '1';          // { show the Free Event ribbon (1 = yes, 0 = no)
   $freeEventColor          = 'orange';     // { Free Event ribbon colour (valid options = navy, green, gold, orange, red, blue, silver)

   // upcoming events options
   $showDaysToGoRibbon      = '1';          // { show a "Days To Go" ribbon message (if not sold-out or on-hold) (1 = yes, 0 = no)
   $numDaysToGo             = '7';          // { number of days before event to show "Days To Go" ribbon message ($showDaysToGo above must = 1)
   $numDaysToGoColor        = 'green';      // { DaysToGo ribbon colour (valid options = navy, green, gold, orange, red, blue, silver)

   // event available ticket options
   $showAvailTickets        = '1';          // show the number of available tickets (1 = yes, 0 = no)
   $availTicketsColor       = 'blue';       // { available tickets ribbon colour (valid options = navy, green, gold, orange, red, blue, silver)

   // advertising/banner options
   // display a module (probably banners or some form of Advert) between event rows?
   // don't forget to add and publish a module (most likely the banners module) to the a position and name it: "inlineEventAdvert"
   // plus setup any banners you wish to display between the event rows
   $showModule              = '1';          // 1 = yes, 0 = no
   $showModuleEvery         = '3';          // display every 'X' events (valid options = 1, 2, 3, 4 etc, depending on how many events are published)
  /** END USER CONFIGURABLE OPTIONS **************************************************/
