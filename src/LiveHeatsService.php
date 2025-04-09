<?php

namespace LiveHeats;

class LiveHeatsService
{
    private GraphQLClient $client;

    public function __construct(GraphQLClient $client)
    {
        $this->client = $client;
    }

    public function getOrganisationByShortName(string $shortName): array
    {
        if (empty($shortName)) {
            throw new \InvalidArgumentException("shortName cannot be empty.");
        }

        $query = <<<GQL
        query getOrganisationByShortName(\$shortName: String) {
        organisationByShortName(shortName: \$shortName) {
            id
            name
            shortName
            logo
            instagram
            facebook
            events {
            id
            name
            status
            date
            }
        }
        }
        GQL;

        return $this->client->query($query, ['shortName' => $shortName]);
    }

    public function getEventById(int $eventId): array
    {
        if ($eventId <= 0) {
            throw new \InvalidArgumentException("Invalid event ID");
        }

        $query = <<<GQL
        query getEvent(\$id: ID!) {
        event(id: \$id) {
            id
            name
            status
            date
            organisation {
            id
            name
            shortName
            logo
            }
            series {
            id
            name
            }
            eventDivisions {
            id
            entryCount
            division {
                id
                name
            }
            }
            location {
            placePredictionText
            googleMapsUri
            }
        }
        }
        GQL;

        return $this->client->query($query, ['id' => $eventId]);
    }

    public function getEventDivisionById(int $eventDivisionId): array
    {
        if ($eventDivisionId <= 0) {
            throw new \InvalidArgumentException("Invalid event division ID");
        }

        $query = <<<GQL
        query getEventDivision(\$id: ID!) {
        eventDivision(id: \$id) {
            id
            status
            heatDurationMinutes
            division {
            id
            name
            }
            heats {
            id
            round
            roundPosition
            position
            startTime
            endTime
            heatDurationMinutes
            result {
                athleteId
                total
                place
            }
            competitors {
                id
                athleteId
                bib
                athlete {
                id
                name
                image
                }
            }
            }
            sponsoredContents {
            id
            sponsor {
                id
                config {
                name
                text
                image
                url
                }
            }
            }
        }
        }
        GQL;

        return $this->client->query($query, ['id' => $eventDivisionId]);
    }

    public function getEventDivisionRanking(int $id, ?int $eventDivisionId = null): array
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid ID");
        }

        $query = <<<GQL
        query getEventDivisionRank(\$id: ID!, \$eventDivisionId: ID) {
        eventDivision(id: \$id) {
            id
            division {
            id
            name
            }
            ranking(eventDivisionId: \$eventDivisionId) {
            id
            place
            total
            excluded
            rides
            competitor {
                id
                athleteId
                bib
                teamName
                athlete {
                id
                name
                image
                }
            }
            }
        }
        }
        GQL;

        return $this->client->query($query, [
            'id' => $id,
            'eventDivisionId' => $eventDivisionId
        ]);
    }

    public function getSeriesWithRankingDivisions(string $shortName): array
    {
        if (empty($shortName)) {
            throw new \InvalidArgumentException("shortName cannot be empty.");
        }

        $query = <<<GQL
        query getOrganisationByShortName(\$shortName: String) {
        organisationByShortName(shortName: \$shortName) {
            series {
            id
            name
            rankingsDivisions {
                id
                name
            }
            }
        }
        }
        GQL;

        $response = $this->client->query($query, ['shortName' => $shortName]);

        $seriesList = $response['organisationByShortName']['series'] ?? [];

        // Structure result for convenience
        return array_map(function ($series) {
            return [
                'series_id' => $series['id'],
                'series_name' => $series['name'],
                'rankings_divisions' => array_map(function ($div) {
                    return [
                        'id' => $div['id'],
                        'name' => $div['name']
                    ];
                }, $series['rankingsDivisions'] ?? [])
            ];
        }, $seriesList);
    }

    public function getSeriesRankings(int $seriesId, int $divisionId, ?string $filter = null): array
    {
        if ($seriesId <= 0 || $divisionId <= 0) {
            throw new \InvalidArgumentException("Series ID and Division ID must be positive integers.");
        }

        $query = <<<GQL
        query getSeriesRankings(\$id: ID!, \$divisionId: ID!, \$filter: String) {
        series(id: \$id) {
            id
            rankings(divisionId: \$divisionId, filter: \$filter) {
            athlete {
                id
                name
                image
            }
            displayProperty
            place
            points
            }
        }
        }
        GQL;

        $response = $this->client->query($query, [
            'id' => $seriesId,
            'divisionId' => $divisionId,
            'filter' => $filter
        ]);

        return $response['series']['rankings'] ?? [];
    }

    public function getAthleteSeriesResults(int $seriesId, int $divisionId, int $athleteId, ?string $filter = null): array
    {
        if ($seriesId <= 0 || $divisionId <= 0 || $athleteId <= 0) {
            throw new \InvalidArgumentException("Series ID, Division ID, and Athlete ID must be positive integers.");
        }

        $query = <<<GQL
        query getSeriesResults(\$id: ID!, \$divisionId: ID!, \$athleteId: ID!, \$filter: String) {
        series(id: \$id) {
            id
            athleteRankingResults(divisionId: \$divisionId, athleteId: \$athleteId, filter: \$filter) {
            resultsToCount
            eligibleResults
            eventsToCount
            results {
                id
                place
                points
                dropped
                eventDivision {
                id
                event {
                    id
                    name
                    date
                }
                }
            }
            }
        }
        }
        GQL;

        $response = $this->client->query($query, [
            'id' => $seriesId,
            'divisionId' => $divisionId,
            'athleteId' => $athleteId,
            'filter' => $filter
        ]);

        return $response['series']['athleteRankingResults'] ?? [];
    }
    public function getEventDivisions($eventId)
    {
        $query = <<<'GRAPHQL'
            query GetEventDetails($eventId: ID!) {
            event(id: $eventId) {
                id
                name
                eventDivisions {
                id
                status
                entryCount
                division {
                    id
                    name
                }
                }
            }
            }
            GRAPHQL;

        // Set up the variables for the query.
        $variables = ['eventId' => $eventId];

        return $this->client->query($query,  $variables);
    }  

    
}
