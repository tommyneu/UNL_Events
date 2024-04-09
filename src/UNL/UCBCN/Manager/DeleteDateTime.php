<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Event\Occurrences as Occurrences;
use UNL\UCBCN\Permission;

class DeleteDateTime extends PostHandler
{
    public $options = array();
    public $calendar;
    public $event;
    public $event_datetime;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_EDIT_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (!$this->event->userCanEdit()) {
            throw new \Exception("You do not have permission to edit this event.", 403);
        }

        $this->event_datetime = Occurrence::getByID($this->options['event_datetime_id']);

        if ($this->event_datetime === FALSE) {
            throw new \Exception("That datetime could not be found", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if (!isset($post['event_datetime_id'])) {
            throw new \Exception("The event_datetime_id must be set in the post data", 400);
        }

        if ($post['event_datetime_id'] != $this->event_datetime->id) {
            throw new \Exception("The event_datetime_id in the post data must match the event_datetime_id in the URL", 400);
        }

        $event = Event::getById($this->event_datetime->event_id);
        $allOccurrences = new Occurrences(array(
            'event_id' => $event->id,
        ));

        if (count($allOccurrences) === 1) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Date/Time Delete Error', 'You need at least one Date/Time instance for your event.');
            // Redirect to the event edit page
            return $this->event->getEditURL($this->calendar);
        }

        $this->event_datetime->delete();

        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Date/Time Deleted', 'A Date/Time for your event ' . $this->event->title . ' has been deleted.');

        // Redirect to the event edit page
        return $this->event->getEditURL($this->calendar);
    }
}