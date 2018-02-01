@extends('users.index')

@section('page_title', 'Sportolói adataim frissítése')

@section('main_content')
<div class="conainer-fluid mt-2">
  <div class="row justify-content-center">
    <div class="col-11">
      <h4 class="text-right bg-primary text-white py-1 px-2">Adatlap szerkesztése</h4>
      <hr>
      <form action="{{ url('update') }}" method="POST" novalidate="true" id="updateUserDetails" enctype='multipart/form-data'>

        {{-- hidden inputs for savin the form as as a user profile update and user history
          update--}}
        {{ csrf_field() }}
        <input type="hidden" name="_method" class="player-history" value="PUT">
        <input type="hidden" name="user_id" class="player-history" value="{{ $user->id }}">
        <input type="hidden" name="user_type_id" class="player-history" value="{{ $user->user_type_id }}">

        @if ( !empty($errors->all() ) )
          @foreach ($errors->all() as $error)
          <div class="row justify-content-end">
            <div class="col-12">
              <div class="alert alert-danger p-2">
                {{ $error }}
              </div>
            </div>
          </div>
          @endforeach
        @endif

        <h4>Személyes adatok</h4>
        <div class="form-row">

          @include('components.last_name')

          @include('components.first_name')

          @include('components.middle_name')

        </div>

        <div class="form-row">

          @include('components.place_of_birth')

          @include('components.country_of_birth')

          @include('components.date_of_birth')

        </div>

        <div class="form-row">

          @include('components.sex')

          @include('components.sport')
<script>
  $('#sport').prop({
    'readonly' : true,
    'disabled' : true
  })
</script>
        </div>


        <hr>
        <h4 class="mt-4 mb-4">Profilképem</h4>
        <div class="form-row justify-content-between">

          @include('components.profile_photo')

        </div>


        <hr>
        <h4 class="mt-4 mb-4">Átigazolási állapotom<sup class="text-danger"><strong>*</strong></sup></h4>
        <div class="form-row transfer_status">

          @include('components.transfer_status')

        </div>
        


        <hr>
        <h4 class="mt-4 mb-4">Sportolói alapadataim</h4>
        <div class="form-row">
          
          @include('components.height')

          @include('components.weight')

          @include('components.preferred_side')
          
        </div>

        <hr>

        <h4 class="mt-4 mb-4">Beszélt nyelvek<sup class="text-danger"><strong>*</strong></sup></h4>

        <div class="form-row">

          @include('components.spoken_languages')

        </div>

        <hr>

        <h4 class="mt-4 mb-4">Itt szeretnék játszani<sup class="text-danger"><strong>*</strong></sup></h4>

        <div class="form-row">
          
          @include('components.target_countries')

        </div>

        <hr>

        <h4 class="mt-4 mb-4">Sportolói múltam<sup class="text-danger"><strong>*</strong></sup></h4>

        <div class="form-row player-history">
          <table class="table table-striped table-hover table-sm table-responsive-md">
            <thead class="bg-dark text-white text-center font-weight-bold text-lowercase">
              <td>#</td>
              <td>szezon</td>
              <td>csapat</td>
              <td>korosztály</td>
              <td>liga</td>
              <td>meccs</td>
              <td>{{ ($user->sport_id == 2) ? 'pontok' : 'gólok'}}</td>
              <td></td>
              <td></td>
            </thead>
            <tbody id="player-history-container">
                @include('users.carriers.player')
            </tbody>
          </table>
        </div>

        <div class="row mt-5 justify-content-end">
          <div class="col-auto">
            <button type="button" name="new_player_history" id="new_player_history" class="btn btn-primary btn-block"
              data-toggle="modal"
              data-target="#userCarrierModal"
              data-title="Új szezon mentése"
              data-userid="{{ $user->id }}"
              data-usertypeid="{{ $user->user_type_id }}"
              data-submit="mentés"
              data-action="saveUserCarrier(event)">
              <i class="fa fa-plus-square mr-auto" aria-hidden="true"></i> új szezon</button>
          </div>
        </div>

        <hr>
        <h4 class="mt-4 mb-4">Játszott posztok <sup class="text-danger"><strong>*</strong></sup></h4>

        <?php
        // get all positions, which is played by the user
        $played_positions = [];
        foreach ($user->position as $pos){
          array_push($played_positions, $pos->id);
        }
        ?>

        @if ($user->sport_id == 1)
          @include('users.update.roles.handball')
        @elseif ($user->sport_id == 2)
          @include('users.update.roles.basketball')
        @elseif ($user->sport_id == 3)
          @include('users.update.roles.football')
        @endif

        <div class="form-row">
          <div class="form-group col-12 m-2 text-center">
            <input type="text" name="position_check" class="invisible form-control" id="position_check" required>
            <div class="invalid-feedback">Kérem adja meg melyik poszton/posztokon játszik!</div>
          </div>
        </div>

        <div class="form-group row mt-5 justify-content-between">

          @include('components.submit')

        </div>

      </form>
    </div>
  </div>
</div>
<script src="{!! asset('js/datepicker.js') !!}"></script>
<script src="{!! asset('js/handle-player-carrier-modal.js') !!}"></script>
<script src="{!! asset('js/handle-spoken-language-change.js') !!}"></script>
<script src="{!! asset('js/handle-target-country-change.js') !!}"></script>
<script src="{!! asset('js/handle-load-image.js') !!}"></script>
<script src="{!! asset('js/profile-image-preview.js') !!}"></script>
<script src="{!! asset('js/validate-player-and-expert-update.js') !!}"></script>

@include('users.update.player_modals')

@stop
