<?php declare(strict_types=1);

/**
 * Allows the extraction of public object properties
 * 
 * This is handy for getting properties that are accessible publicly from 
 * inside the object.
 */
function public_object_vars(object $item) : array {
    return get_object_vars($item);
}

/**
 * Turns xml into an array
 *
 * @param object|string $xml - The xml document or text to turn into an array.
 */
function xml_to_array($xml, array $options = []) : array {
    $defaults = array(
        'namespaceSeparator' => ':',//you may want this to be something other than a colon
        'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
        'alwaysArray' => [],   //array of xml tag names which should always become arrays
        'autoArray' => true,        //only create arrays for tags which appear more than once
        'textContent' => '$',       //key used for the text content of elements
        'autoText' => true,         //skip textContent key if node has no attributes or child nodes
        'keySearch' => false,       //optional search and replace on tag and attribute names
        'keyReplace' => false       //replace values for above search values (as passed to str_replace())
    );
    $options = array_merge($defaults, $options);

    if (is_string($xml)) {
        $xml = simplexml_load_string($xml);
    }

    $namespaces = $xml->getDocNamespaces();
    $namespaces[''] = null; //add base (empty) namespace

    //get attributes from all namespaces
    $attributesArray = [];
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
            //replace characters in attribute name
            if ($options['keySearch']) {
                $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);   
            }
            $sep = $prefix ? $prefix . $options['namespaceSeparator'] : '';
            $attributeKey = $options['attributePrefix'] . $sep . $attributeName;
            $attributesArray[$attributeKey] = (string)$attribute;
        }
    }

    //get child nodes from all namespaces
    $tagsArray = [];
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->children($namespace) as $childXml) {
            //recurse into child nodes
            $childArray = xml_to_array($childXml, $options);
            list($childTagName, $childProperties) = [key($childArray), current($childArray)];

            //replace characters in tag name
            if ($options['keySearch']) {
                $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
            }
            //add namespace prefix, if any
            if ($prefix) {
                $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
            }

            if (!isset($tagsArray[$childTagName])) {
                //only entry with this key
                //test if tags of this type should always be arrays, no matter the element count
                if(in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']) {
                    $tagsArray[$childTagName] = [$childProperties];
                } else {
                    $tagsArray[$childTagName] = $childProperties;
                }
            } elseif (
                is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                === range(0, count($tagsArray[$childTagName]) - 1)
            ) {
                //key already exists and is integer indexed array
                $tagsArray[$childTagName][] = $childProperties;
            } else {
                //key exists so convert to integer indexed array with previous value in position 0
                $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
            }
        }
    }

    //get text content of node
    $textContentArray = [];
    $plainText = trim((string)$xml);
    if ($plainText !== '') {
        $textContentArray[$options['textContent']] = $plainText;
    }

    //stick it all together

    if(!$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')) {
        $propertiesArray = array_merge($attributesArray, $tagsArray, $textContentArray);
    } else {
        $propertiesArray = $plainText;
    }

    //return node as array
    return [
        $xml->getName() => $propertiesArray
    ];
}
