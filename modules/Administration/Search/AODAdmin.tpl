<h1>{$MOD.LBL_AOD_ADMIN_MANAGE_AOD}</h1>

<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">{$MOD.LBL_SEARCH_GENERAL}</div>
        <div class="panel-body tab-content text-center">
            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input"
                           id="enable_aod" name="enable_aod"
                           {if $aod_config.enable_aod}checked='checked'{/if}>
                    <label class="form-check-label" for="enable_aod">{$MOD.LBL_AOD_ENABLE}</label>
                </div>
            </div>
        </div>
    </div>
</div>
