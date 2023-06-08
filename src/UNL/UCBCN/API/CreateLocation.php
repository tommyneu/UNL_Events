<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Location;

class CreateLocation {
    public $options = array();
    public $location;
    public $result;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    public function handleGet($get)
    {
        throw new NotFoundException('Not Found');
    }

    public function handlePost($post)
    {
        $location = $this->createLocation($post);
        return $location;
    }

    private function validateLocationData($post_data)
    {
        # name required
        if (empty($post_data['name'])) {
            throw new ValidationException('Name is required.');
        }

        if (empty($post_data['new_location']['streetaddress1'])) {
            throw new ValidationException('Address is required.');
        }

        if (empty($post_data['new_location']['city'])) {
            throw new ValidationException('City is required.');
        }

        if (empty($post_data['new_location']['state'])) {
            throw new ValidationException('state is required.');
        }

        if (empty($post_data['new_location']['zip'])) {
            throw new ValidationException('Zip is required.');
        }

        if (!empty($post_data['new_location']['webpageurl']) &&
            !filter_var($post_data['new_location']['webpageurl'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Location URL is not a valid URL.');
        }
    }

    private function createLocation($post_data)
    {
        $this->validateLocationData($post_data);

        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'room',
            'city',
            'state',
            'zip',
            'mapurl',
            'webpageurl',
            'hours',
            'directions',
            'additionalpublicinfo',
            'type',
            'phone',
        );

        $location = new Location;

        foreach ($post_data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                if (!empty($value)) {
                    $location->$field = $value;
                }
            }
        }

        $location->standard = 0;
        $location->insert();
        return $location;
    }
}