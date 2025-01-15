@extends('layouts.export')

@php($imageBasePath = $debug ? '' : public_path())

@section('head_content')
    @include('export._style')
@endsection

@section('body')
    <div id="agape-logo-wrapper">
        <img src="{{ $imageBasePath }}/{{ env('APP_LOGO') }}" alt="{{ config('app.name') }}" id="agape-logo">
    </div>
    <h3 class="text-center">
        {!! __('admin.application.export_name') !!}
    </h3>

    @foreach ($data as $section)
        <section>
            <h3 class="text-center">
                {{ $section['title'] }}
            </h3>
            {{-- Display application data in a table --}}
            <table class="w-full">
                @foreach ($section['fields'] as $field)
                    <tr>
                        @if (filled($field['label']))
                            <th class="w-20">
                                {{ $field['label'] }}
                            </th>
                        @endif
                        @if (!is_array($field['value']))
                            <td style="padding: 10px;" @if (blank($field['label'])) colspan="2" @endif>
                                {!! $field['value'] !!}
                            </td>
                        @else
                            <td style="padding: 10px;" @if (blank($field['label'])) colspan="2" @endif>
                                @if (is_array(head($field['value'])))
                                    <table class="w-full avoid-page-break">
                                        @foreach ($field['value'] as $subfield)
                                            <tr>
                                                @if (filled($subfield['label'] ?? null))
                                                    <th class="w-20">
                                                        {{ $subfield['label'] }}
                                                    </th>
                                                @endif
                                                @if (!is_array($subfield['value'] ?? []))
                                                    <td style="padding: 10px;">
                                                        {{ $subfield['value'] }}
                                                    </td>
                                                @else
                                                    <td style="padding: 10px;">
                                                        @if (is_array(head($subfield['value'])))
                                                            <table class="w-full avoid-page-break">
                                                                @foreach ($subfield['value'] as $subsubfield)
                                                                    <tr>
                                                                        <th>
                                                                            {{ $subsubfield['label'] }}
                                                                        </th>
                                                                        <td>
                                                                            {{ $subsubfield['value'] }}
                                                                        </td>
                                                                @endforeach
                                                            </table>
                                                        @else
                                                            <ul>
                                                                @foreach ($subfield['value'] as $value)
                                                                    <li>{{ $value }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <ul class="avoid-page-break">
                                        @foreach ($field['value'] as $value)
                                            <li>{!! $value !!}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </section>
    @endforeach

    <script type="text/php">
        if (isset($pdf)) {
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width);
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
@endsection
