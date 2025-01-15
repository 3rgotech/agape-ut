<style>
    * {
        font-family: Helvetica, Arial, sans-serif;
    }

    @page {
        size: A4 portrait;
    }

    section,
    .avoid-page-break {
        page-break-inside: avoid;
    }

    table,
    table tr,
    table td,
    table th {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .page-break {
        page-break-after: always;
    }

    .w-full {
        width: 100%;
    }

    .text-center {
        text-align: center;
    }

    .title-bordered {
        border: 2px solid black;
    }

    .evaluation_criteria,
    .row {
        page-break-inside: avoid;
        display: block;
    }

    .flex-row {
        display: flex;
        align-items: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .w-20 {
        width: 20%;
    }

    .col-3 {
        width: 20%;
        display: inline;
        font-weight: bold;
        text-decoration: underline;
    }

    .col-9 {
        width: 80%;
        display: inline;
        text-align: justify;
    }

    .col-4 {
        width: 33%;
        display: inline;
        font-weight: bold;
        text-decoration: underline;
    }

    .col-8 {
        width: 66%;
        display: inline;
        text-align: justify;
    }

    p.lead,
    .col-9>* {
        text-align: justify;
    }

    #agape-logo-wrapper {
        text-align: center;
    }

    #agape-logo {
        width: 70%;
    }

    .reference-table {
        width: 100%;
    }

    .reference-table th,
    .reference-table td,
    .evaluation {
        font-size: 10pt;
    }

    .evaluation .evaluation_criteria h5 {
        font-size: 110%;
    }
</style>
