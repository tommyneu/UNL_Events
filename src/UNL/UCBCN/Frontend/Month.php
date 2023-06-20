<?php
/**
 * This class contains the information needed for viewing a month view calendar.
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
 */
namespace UNL\UCBCN\Frontend;

use Traversable;

/**
 * Object for a month view of the calendar.
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
class Month implements \IteratorAggregate, RoutableInterface, MetaTagInterface
{
    /**
     * Calendar to show events for UNL_UCBCN_Month object
     *
     * @var \UNL\UCBCN\Frontend\Calendar 
     */
    public $calendar;

    public $options = array(
            'm' => null,
            'y' => null,
            );

    /**
     * Configurable start day for the week
     *
     * @var string
     */
    public static $weekday_start = 'Sunday';

    /**
     * Constructor for an individual day.
     *
     * @param array $options Associative array of options to apply.
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        if (!isset($options['calendar'])) {
            throw new InvalidArgumentException('A calendar must be set', 500);
        }

        $this->calendar = $options['calendar'];

        // Set defaults
        $this->options['m'] = date('m');
        $this->options['y'] = date('Y');

        $this->options = $options + $this->options;
    }

    /**
     * Get the date period object for this month
     * 
     * @return \DatePeriod
     */
    public function getDatePeriod()
    {
        $start_date = $this->getStartDateTime();
        $end_date   = $this->getEndDateTime();
        $interval   = new \DateInterval('P1D');
        
        return new \DatePeriod($start_date, $interval, $end_date);
    }
    
    public function getIterator(): Traversable
    {
        return new DayIterator($this->getDatePeriod(), $this->calendar);
    }

    /**
     * Get the starting datetime object for this month
     *
     * @return \DateTime
     */
    public function getStartDateTime()
    {
        $first_day = new \DateTime(
                $this->options['y'].'-'.$this->options['m'].'-01'
        );
        if (self::$weekday_start != $first_day->format('l')) {
            $first_day = $first_day->modify('last '.self::$weekday_start);
        }
        return $first_day;
    }

    /**
     * Get the ending datetime object for this month
     *
     * @return \DateTime
     */
    public function getEndDateTime()
    {
        $last_day = new \DateTime(
               $this->options['y'].'-'.$this->options['m'].'-01 +1 month'
        );

        if (self::$weekday_start != $last_day->format('l')) {
            $last_day->modify('next '.self::$weekday_start);
        }

        return $last_day;
        
    }

    /**
     * Get the date for this month
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime(
                $this->options['y'].'-'.$this->options['m'].'-01'
        );
    }

    /**
     * Get a relative month
     * 
     * @param $string - +1, -1, etc
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getRelativeMonth($string)
    {
        $datetime = $this->getDateTime()->modify($string . ' month');

        $options = $this->options;
        $options['m'] = $datetime->format('m');
        $options['y'] = $datetime->format('Y');

        $class = get_called_class();

        return new $class($options);
    }

    /**
     * Get the previous month object
     *
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getPreviousMonth()
    {
        return $this->getRelativeMonth('-1');
    }

    /**
     * Get the next month object
     *
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getNextMonth()
    {
        return $this->getRelativeMonth('+1');
    }

    /**
     * Get the year for this month
     * 
     * @return \UNL\UCBCN\Frontend\Year Year
     */
    public function getYear()
    {
        return new Year($this->options);
    }

    /**
     * Returns the permalink URL to this specific month.
     *
     * @return string URL to this day.
     */
    public function getURL()
    {
        return self::generateURL($this->calendar, $this->getDateTime());
        
    }

    public function getMetaTags()
    {
        $metaTagOutput = "";

        $metaTagOutput .= '<meta property="og:title" content="' . $this->calendar->name . ' Calendar - ' . $this->getDateTime()->format('F') . '" />' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:url" content="' . $this->getURL() . '" />' . PHP_EOL;
        $metaTagOutput .= '<meta property="og:type" content="website" />' . PHP_EOL;

        return $metaTagOutput;
    }

    /**
     * Generate a Month URL for a specific calendar and date
     *
     * @param Calendar $calendar
     * @param \DateTime $datetime
     * @return string
     */
    public static function generateURL(Calendar $calendar, \DateTime $datetime)
    {
        return $calendar->getURL() . $datetime->format('Y/m') . '/';
    }

    /**
     * Get a permalink URL for the context's previous month
     *
     * @return string
     */
    public function getPreviousMonthURL()
    {
        return self::generateURL($this->calendar, $this->getDateTime()->modify('-1 month'));
    }

    /**
     * get a permalink URL for the context's next month
     *
     * @return string
     */
    public function getNextMonthURL()
    {
        return self::generateURL($this->calendar, $this->getDateTime()->modify('+1 month'));
    }

    /**
     * Get a permalink URL for the context's year
     *
     * @return string
     */
    public function getYearURL()
    {
        return Year::generateURL($this->calendar, $this->getDateTime());
    }
}
