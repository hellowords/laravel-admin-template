<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> {{ config('website.version') }}
    </div>
    <strong>Copyright &copy;
        {{ date('Y') == '2018' ? '2018': '2018 -'.date('Y') }}  <a href="https://happyhack.cn">Happyhack Studio</a>.</strong> All rights
    reserved.
</footer>