# wms-pull-list-php

PHP wrapper for OCLC's [WMS Pull List Resource](http://www.oclc.org/developer/develop/web-services/wms-circulation-api/pull-list-resource.en.html).

## usage

1) This is still in process, so you'll have to go the long-way around to get it to work, that means `"minimum-stability": "dev"` and
the repositories for both this library as well as `OCLC/Auth` will have to be in your `composer.json` like so:

```json
{
    "name": "your-name/your-app-name",
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/malantonio/wms-pull-list-php"
        },
        {
            "type": "git",
            "url": "https://github.com/OCLC-Developer-Network/oclc-auth-php"
        }
    ],
    "require": {
        "OCLC/Auth": ">=1.0",
        "malantonio/wms-pull-list": "*"
    },
    "minimum-stability": "dev"
}
```

2) On the command-line

```
> composer install
```

3) In your app

```php
require "vendor/autoload.php";

$wskey = new OCLC\Auth\WSKey("public-wskey", "secret!!");
$branch = 1234;

$items = WMSPullList::getList($branch, $wskey);

$html = "<table>";
$html .= "<thead><tr><td>Title</td><td>Location</td><td>Call Number</td></tr></thead>"
$html .= "<tbody>";

foreach($items as $item) {
    $html .= "<tr>";
    $html .=    "<td>" . $item->bibliographicItem->title . "</td>";
    $html .=    "<td>" . $item->premanentShelvingLocation->element[0] . "</td>;
    $html .=    "<td>" . $item->callNumber->description . "</td>";
    $html .= "</tr>";
}

$html .= "</tbody></table>";

echo $html;
```

## `WMSPullList::getList(int $branch, OCLC\Auth\WSKey $wskey)`

Static wrapper for quick usage. Returns an array of `stdClass` objects that looks something like this when `print_r`'d:

```
[0] => stdClass Object
    (
        [bibliographicItem] => stdClass Object
            (
                [oclcNumber] => 529266
                [title] => Jackson Pollock.
                [author] => O'Hara, Frank,
                [materialFormat] => BOOK
                [publisher] => New York, G. Braziller,
                [publicationYear] => 1959
                [language] => eng
                [edition] =>
            )

        [pieceDesignation] => 31542001803711
        [callNumber] => stdClass Object
            (
                [shelvingScheme] =>
                [shelvingInformation] =>
                [itemParts] => Array
                    (
                    )

                [prefixes] => Array
                    (
                    )

                [suffixes] => Array
                    (
                    )

                [description] => 759.13 P776zo
            )

        [recordType] => SINGLE_PART
        [holdingInformation] =>
        [numberOfPieces] => 1
        [physicalDescription] =>
        [cost] =>
        [homeHoldingLocation] => EVII
        [permanentShelvingLocation] => stdClass Object
            (
                [element] => Array
                    (
                        [0] => Main Collection
                    )

            )

        [previousShelvingLocation] =>
        [temporaryShelvingLocation] =>
        [publicNotes] => Array
            (
            )

        [staffNotes] => Array
            (
            )

        [useRestrictions] => Array
            (
            )

        [requestDate] => 1434216243000
        [patronName] => Malantonio, Adam
        [enumeration] =>
        [freeText] =>
    )
```

## `new WMSPullList($branch, $wskey[, $opts = array()])`

Instantiates a new `PullList`. `$opts` may be an associative array with the possible keys:

      key    |              value              |  default
-------------|---------------------------------|----------
`startIndex` | which index to begin results at |  1
`limit`      | max number of items returned    |  0 (returns all holds)

## `$pl->limit(int $limit)`

Explicitly sets the limit. Returns instance for chaining 

## `$pl->startIndex(int $index)`

Explicitly sets the start index. Returns instance for chaining.

## `$pl->getEntries()`

Sends request for pull-list items and returns an array similar to `WMSPullList::getList`.

## `$pl->getFullResponse()`

Sends request for pull-list items and returns full `json_decode`d results. The actual items are stored in the `$response->entry`
array. The results look like this when `print_r`'d

```
stdClass Object
(
    [entry] => Array
        (
        )
    [startIndex] => 1
    [totalResults] => 11
    [itemsPerPage] => 0
    [id] => urn:oclc:circulation/pulllist/128807/128156
    [title] => Pull List
    [links] => Array
        (
        )

    [updated] => stdClass Object
        (
            [offset] => stdClass Object
                (
                    [id] => Z
                    [amountSeconds] => 0
                    [secondsField] => 0
                    [hoursField] => 0
                    [minutesField] => 0
                )

            [year] => 2015
            [dayOfYear] => 164
            [dayOfMonth] => 13
            [dayOfWeek] => SATURDAY
            [nanoOfSecond] => 716000000
            [chronology] => stdClass Object
                (
                    [name] => ISO
                )

            [monthOfYear] => JUNE
            [hourOfDay] => 17
            [minuteOfHour] => 58
            [secondOfMinute] => 8
        )

    [errors] => stdClass Object
        (
        )

)
```

## License
MIT
