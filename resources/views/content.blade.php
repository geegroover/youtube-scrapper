@extends('layouts.app')

@section('content')
  @if (isset($message)) <p>{{ $message }}</p> @endif

    <!-- <form>
      <input /><label for='input'>  Channel ID</label>
    </form> -->

  @if (isset($data))

    <?php dump($data); ?>
      <table class="table table-bordered">

        <thead>
          <?php $keys = get_object_vars($data[0]); ?>
          @foreach ($keys as $k => $v)
            <th>{{ $k }}</th>
          @endforeach
        </thead>

        <tbody>
            @foreach ($data as $video)
              <tr>
                <?php $values = get_object_vars($video); ?>
                @foreach ($values as $value)
                  <td>{{ $value }}</td>
                @endforeach
              </tr>
            @endforeach
        </tbody>

      </table>

  @else

    <p>Something Went Wrong...</p>

  @endif

@endsection
