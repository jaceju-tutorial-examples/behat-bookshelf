@extends('layouts.app')

@section('content')

    <div class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><span>借書系統</span></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-ex-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="/auth/logout">登出</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">
        <ul class="list-group">
            <li class="list-group-item clearfix">
                <h3 class="pull-left">專案管理實務 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">
                    借書
                </button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">HTML5 + CSS3 專用網站設計 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">JavaScript 學習手冊 <span class="badge alert-success">可借出</span></h3>

                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">精通 VI <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">PHP 聖經 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
        </ul>
    </div> <!-- /container -->

@stop