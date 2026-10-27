<?php

/** Calendar length of one recorded service period, including both service dates. */
function serviceRecordDuration($fromValue, $toValue, DateTimeImmutable $asOfDate)
{
    $from = serviceRecordDate($fromValue);
    $asOf = serviceRecordDate($asOfDate->format('Y-m-d'));
    if ($from === null) {
        return null;
    }

    $toText = trim((string)$toValue);
    $isPresent = $toText === '' || strcasecmp($toText, 'Present') === 0 || $toText === '0000-00-00';
    $to = $isPresent ? $asOf : serviceRecordDate($toText);
    if ($to === null || $from > $asOf || $to < $from) {
        return null;
    }

    // Future end dates have not yet been served.
    if ($to > $asOf) {
        $to = $asOf;
    }

    $elapsed = $from->diff($to->modify('+1 day'));
    return sprintf(
        '%d %s, %d %s, %d %s',
        $elapsed->y, $elapsed->y === 1 ? 'year' : 'years',
        $elapsed->m, $elapsed->m === 1 ? 'month' : 'months',
        $elapsed->d, $elapsed->d === 1 ? 'day' : 'days'
    );
}

function serviceRecordDate($value)
{
    $text = trim((string)$value);
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $text, new DateTimeZone('UTC'));
    return $date && $date->format('Y-m-d') === $text ? $date : null;
}
