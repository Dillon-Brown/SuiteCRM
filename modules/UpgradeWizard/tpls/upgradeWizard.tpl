{literal}
    <script>
      $(document).ready(function () {
        let navListItems = $('ul.setup-panel li a'),
          allWells = $('.setup-content');

        allWells.hide();

        navListItems.click(function (e) {
          e.preventDefault();
          let $target = $($(this).attr('href')),
            $item = $(this).closest('li');

          if (!$item.hasClass('disabled')) {
            navListItems.closest('li').removeClass('active');
            $item.addClass('active');
            allWells.hide();
            $target.show();
          }
        });

        $('ul.setup-panel li.active a').trigger('click');

        $('#activate-step-2').on('click', function (e) {
          $('ul.setup-panel li:eq(1)').removeClass('disabled');
          $('ul.setup-panel li a[href="#step-2"]').trigger('click');
          $(this).remove();
        })
      });
    </script>
{/literal}

<div class="container">
    <div class="row form-group">
        <div class="col-xs-12">
            <ul class="nav nav-justified setup-panel" style="border: 1px solid #e3e3e3;">
                <li class="active"><a href="#step-1">
                        <h4 class="list-group-item-heading">System Check</h4>
                        <p class="list-group-item-text">First step description</p>
                    </a></li>
                <li class="disabled"><a href="#step-2">
                        <h4 class="list-group-item-heading">Upload Upgrade Package</h4>
                        <p class="list-group-item-text">Second step description</p>
                    </a></li>
                <li class="disabled"><a href="#step-3">
                        <h4 class="list-group-item-heading">Preflight Check</h4>
                        <p class="list-group-item-text">Third step description</p>
                    </a></li>
                <li class="disabled"><a href="#step-4">
                        <h4 class="list-group-item-heading">Commit Upgrade</h4>
                        <p class="list-group-item-text">Fourth step description</p>
                    </a></li>
                <li class="disabled"><a href="#step-5">
                        <h4 class="list-group-item-heading">Confirm Layouts</h4>
                        <p class="list-group-item-text">Fifth step description</p>
                    </a></li>
            </ul>
        </div>
    </div>
    <div class="row setup-content" id="step-1">
        <div class="col-xs-12">
            <div class="col-md-12 well text-center">
                <h1> STEP 1</h1>
                <button id="activate-step-2" class="btn btn-primary btn-lg">Activate Step 2</button>
            </div>
        </div>
    </div>
    <div class="row setup-content" id="step-2">
        <div class="col-xs-12">
            <div class="col-md-12 well">
                <h1 class="text-center"> STEP 2</h1>
            </div>
        </div>
    </div>
    <div class="row setup-content" id="step-3">
        <div class="col-xs-12">
            <div class="col-md-12 well">
                <h1 class="text-center"> STEP 3</h1>
            </div>
        </div>
    </div>
    <div class="row setup-content" id="step-4">
        <div class="col-xs-12">
            <div class="col-md-12 well">
                <h1 class="text-center"> STEP 4</h1>
            </div>
        </div>
    </div>
    <div class="row setup-content" id="step-5">
        <div class="col-xs-12">
            <div class="col-md-12 well">
                <h1 class="text-center"> STEP 5</h1>
            </div>
        </div>
    </div>
</div>
