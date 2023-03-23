<?php
/**
 * Search class for frontend users to search for events.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @version   CVS: $id$
 * @link      http://code.google.com/p/unl-event-publisher/
 * @todo      Add searching by eventtype.
 */
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\Calendar\Audiences;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Event;

/**
 * Container for search results for the frontend.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Search extends EventListing implements RoutableInterface
{
    public $search_query = '';
    public $search_event_type = '';
    public $search_event_audience = '';

    public $limit = 100;
    public $offset = 0;
    public $max_limit = array(
        'json' => 500,
        'xml' => 500,
        'default' => 100
    );

    /**
     * Constructs this search output.
     *
     * @param array $options Associative array of options.
     * @throws UnexpectedValueException
     */
    public function __construct($options=array())
    {

        // Removed error for when search query is empty because I think it would be useful
        $this->search_query = $options['q'] ?? "";
        $this->search_event_type = $options['type'] ?? "";
        $this->search_event_audience = $options['audience'] ?? "";

        $format_max_limit = $this->max_limit['default'];
        if (array_key_exists($options['format'], $this->max_limit)) {
            $format_max_limit = $this->max_limit[$options['format']];
        }

        if (!isset($options['limit']) ||
            empty($options['limit']) ||
            intval($options['limit']) > $format_max_limit ||
            intval($options['limit']) <= 0
        ) {
            $options['limit'] = $format_max_limit;
        }

        if (!isset($options['offset']) || empty($options['offset']) ||  intval($options['offset']) <= 0) {
            $options['offset'] = 0;
        }

        $this->limit = $options['limit'] ?? $this->limit;
        $this->offset = $options['offset'] ?? $this->offset;

        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    protected function getSQL()
    {
        $sql = 'SELECT DISTINCT e.id as id, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.unlinked = 0)
                LEFT JOIN event_has_eventtype ON (event_has_eventtype.event_id = event.id)
                LEFT JOIN eventtype ON (eventtype.id = event_has_eventtype.eventtype_id)
                LEFT JOIN event_targets_audience ON (event_targets_audience.event_id = event.id)
                LEFT JOIN audience ON (audience.id = event_targets_audience.audience_id)
                LEFT JOIN location ON (location.id = e.location_id)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND calendar_has_event.status IN ("posted", "archived")
                    AND  (';

        if ($t = $this->getSearchTimestamp()) {
            // This is a time...
            $sql .= 'e.starttime LIKE \''.date('Y-m-d', $t).'%\'';
        } else {
            // Do a textual search.
            $sql .=
                '(event.title LIKE \'%'.self::escapeString($this->search_query).'%\' OR '.
                '(eventtype.name LIKE \'%'.self::escapeString($this->search_query).'%\') OR '.

                'event.description LIKE \'%'.self::escapeString($this->search_query).'%\' OR '.
                '(location.name LIKE \'%'.self::escapeString($this->search_query).'%\')) AND '.
                'IF (recurringdate.recurringdate IS NULL,
                    e.starttime,
                    CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                ) >= NOW() OR
                IF (recurringdate.recurringdate IS NULL,
                    e.endtime,
                    CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.endtime," %H:%i:%s"))
                ) >= NOW()';
        }

        // Adds filter for event type
        if (!empty($this->search_event_type)) {
            $sql .= ') AND ( eventtype.name = \'' . self::escapeString($this->search_event_type) .'\'';
        }

        // Adds filters for target audience
        if (!empty($this->search_event_audience)) {
            $sql .= ') AND ( audience.name = \'' . self::escapeString($this->search_event_audience) . '\'';
        }

        $sql .= ') ORDER BY (
                        IF (recurringdate.recurringdate IS NULL,
                          e.starttime,
                          CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                        )
                    ) ASC,
                    event.title ASC';

        return $sql;
    }

    /**
     * Gets list of all event types
     *
     * @return bool|EventTypes - false if no event type, otherwise return recordList of all event types
     */
    public function getEventTypes()
    {
        return new EventTypes(array('order_name' => true));
    }

    /**
     * Gets list of all audiences
     *
     * @return bool|Audiences - false if no audiences, otherwise return recordList of all audiences
     */
    public function getAudiences()
    {
        return new Audiences(array('order_name' => true));
    }

    /**
     * Determine the unix timestamp of the search
     *
     * @return bool|int - false if not a date search, otherwise return the unix timestamp of the date search
     */
    public function getSearchTimestamp()
    {
        if (($t = strtotime($this->search_query)) && ($this->search_query != 'art')) {
            // This is a time...
            return $t;
        }

        return false;
    }

    /**
     * returns the url to this search page.
     *
     * @return string
     */
    public function getURL()
    {
        $url = $this->options['calendar']->getURL() . 'search/';

        if (!empty($this->search_query)) {
            $url .= '?q=' . urlencode($this->search_query);
        }

        if (!empty($this->search_event_type)) {
            $url .= '&type=' . urlencode($this->search_event_type);
        }

        if (!empty($this->search_event_audience)) {
            $url .= '&audience=' . urlencode($this->search_event_audience);
        }

        return $url;
    }

    /**
     * Get the month widget for the context's month
     *
     * @return MonthWidget
     */
    public function getMonthWidget()
    {
        return new MonthWidget($this->options);
    }

}
