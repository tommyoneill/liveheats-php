<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LiveHeats\GraphQLClient;
use LiveHeats\LiveHeatsService;

$client = new GraphQLClient('https://liveheats.com/api/graphql');
$service = new LiveHeatsService($client);

try {

    echo "\n🏅 Event Division Ranking (634403):\n";
    print_r($service->getEventDivisionRanking(653583));
    die();

    $org = $service->getOrganisationByShortName('usasa');

    foreach ($org["organisationByShortName"]["events"] as $event) {
        echo "{$event['id']}, '{$event['name']}' , '{$event['date']}'\n" ;
    }

    //output a csv file
    $csvFile = fopen('events.csv', 'w');
    fputcsv($csvFile, ['ID', 'Name', 'Date']);
    foreach ($org["organisationByShortName"]["events"] as $event) {
        $mysqldate = date('Y-m-d H:i:s', strtotime($event['date']));
        fputcsv($csvFile, [$event['id'], $event['name'], $mysqldate]);
    }
    fclose($csvFile);
    echo "CSV file created: events.csv\n";

    die();

    echo "\n🎯 Athlete Results in Series (Series: 39575, Division: 231238, Athlete: 1396662):\n";
    $results = $service->getAthleteSeriesResults(39575, 231238, 1396662);

    foreach ($results['results'] as $r) {
        $event = $r['eventDivision']['event'];
        $dropped = $r['dropped'] ? ' (Dropped)' : '';
        echo "{$event['name']} ({$event['date']}): Place {$r['place']}, {$r['points']} pts$dropped\n";
    }

    echo "\n🏆 Series Rankings (Series ID: 39575, Division ID: 231238):\n";
    $rankings = $service->getSeriesRankings(39575, 231238);

    foreach ($rankings as $rank) {
        echo "#{$rank['place']} - {$rank['athlete']['name']} ({$rank['points']} pts)\n";
    }

    die();

    echo "\n📈 Series with Ranking Divisions (usasaums):\n";
    $series = $service->getSeriesWithRankingDivisions('usasaums');
    print_r($series);
    
    echo "\n🏅 Event Division Ranking (634403):\n";
    print_r($service->getEventDivisionRanking(653583));

    echo "\n📊 Event Division by ID (634403):\n";
    print_r($service->getEventDivisionById(634403));

    echo "\n📢 Organisation by short name (usasa):\n";
    print_r($service->getOrganisationByShortName('usasa'));

    echo "\n🎯 Event by ID (348131):\n";
    print_r($service->getEventById(348131));

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

function athleteDetails()
{
    echo "\n👤 Athlete Details (ID: 1396662):\n";
    $athlete = $service->getAthleteDetails(1396662);

    echo "Name: {$athlete['name']}\n";
    echo "DOB: {$athlete['dob']}\n";
    echo "Nationality: {$athlete['nationality']}\n";
    echo "Injury Status: {$athlete['injuryStatus']}\n\n";

    echo "Entries:\n";
    foreach ($athlete['entries'] as $entry) {
        $event = $entry['eventDivision']['event'];
        $division = $entry['eventDivision']['division'];
        echo "- {$event['name']} ({$event['date']}) in {$division['name']} [{$entry['status']}]\n";
    }

    echo "\nRanks:\n";
    foreach ($athlete['ranks'] as $rank) {
        echo "- EventDivision ID: {$rank['eventDivisionId']} — Place {$rank['place']} ({$rank['total']} pts)\n";
    }
    die();
}
