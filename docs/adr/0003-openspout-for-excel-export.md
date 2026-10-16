# ADR-0003: OpenSpout for Excel Export

## Status
Accepted

## Context
The reporting modules (`Laporan`, `Cetak`) in `cater_web_ci4` frequently export large datasets of meter reading results, variance reports, and condition summaries to Excel/CSV format. Traditional PHP Excel libraries often consume excessive memory and cause HTTP timeouts when processing large batch exports.

## Decision
We selected **OpenSpout** (`openspout/openspout`) as the standard spreadsheet generation library for all Excel and CSV export operations.

## Consequences
- **Positive**: Extremely low memory footprint through stream-based reading and writing, high performance on large datasets, and active support for modern spreadsheet formats.
- **Negative / Trade-offs**: Streaming API requires structuring export data in sequential rows rather than random-access cell manipulation.

## Alternatives Considered
- **PhpSpreadsheet**: Considered as the legacy standard in PHP, but rejected due to its notoriously high memory consumption and slow performance when exporting large datasets.
- **Native CSV Export**: Considered for simplicity, but rejected because users specifically required formatted Excel (`.xlsx`) spreadsheets for official reporting.
