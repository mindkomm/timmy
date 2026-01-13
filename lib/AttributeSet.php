<?php

namespace Timmy;

/**
 * Class AttributeSet
 *
 * Manages a set of HTML attributes.
 */
class AttributeSet {
    protected array $html_attributes = [];

	/**
     * Sets an HTML attribute on the image
     *
     * @param string $attribute
     * @param string $value
     *
     * @return void
     */
    public function set( string $attribute, string $value ) {
        $this->html_attributes[$attribute] = $value;
    }

    /**
     * Gets an attribute.
     *
     * @param string $attribute
     *
     * @return mixed|null
     */
    public function get( string $attribute ) {
        return $this->html_attributes[$attribute] ?? null;
    }

    /**
     * Gets all attributes, excluding empty ones.
     *
     * The alt attribute is always preserved.
     */
    public function get_all() {
        return array_filter($this->html_attributes, static function($value, $key) {
			// Always preserve alt attribute.
			if ($key === 'alt') {
				return true;
			}

            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Removes an attribute.
     *
     * @param string $attribute
     *
     * @return void
     */
    public function remove( string $attribute ) {
        unset($this->html_attributes[$attribute]);
    }

    /**
     * Renames an attribute.
     *
     * @param string $old_attribute
     * @param string $new_attribute
     *
     * @return void
     */
    public function rename( string $old_attribute, string $new_attribute ) {
        if (isset($this->html_attributes[$old_attribute])) {
            $this->html_attributes[$new_attribute] = $this->html_attributes[$old_attribute];
            unset($this->html_attributes[$old_attribute]);
        }
    }
}
