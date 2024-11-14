<div class="container">
    <div class="d-flex mb-4">
        <div class="ms-auto me-auto">Конструктор PDF</div>
    </div>
    @vite(['resources/js/modules/form.js'])
    <form action="{{route('formReciever')}}" enctype="multipart/form-data" method="post">
        @csrf
        <input type="text" name="title" class="form-control mb-3" placeholder="Название" style="width: 100%" value="{{$title ?? ''}}"/>
        <div class="d-flex">
            <div class="ms-auto me-auto"><h4>Раздел ПДФ</h4></div>
        </div>
        @php
            $fileGroups = !empty($fileUploadGroup) ? $fileUploadGroup : [[]];
        @endphp
        @foreach($fileGroups as $key => $fileGroup)
            <div class="d-flex file-upload-group">
                <div class="col-10">
                    <div class="mb-3">
                        <input type="hidden" class="fileModifiedTime" name="fileModifiedTime[]" value="{{$fileGroup['fileModifiedTime'] ?? ''}}">
                        @if(!empty($fileGroup['fileName']))
                            <div class="exists-file-info">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="exist-file-name">Загружен файл: {{$fileGroup['fileName']}}</div>
                                    <input type="button" class="btn btn-secondary replace-file-info" value="Заменить файл">
                                </div>
                            </div>
                            <input style="display: none;" type="file" name="file[]" class="form-control fileInput" value="">
                        @else
                            <input type="file" name="file[]" class="form-control fileInput" value="">
                        @endif
                        <input type="hidden" class="fileName" name="fileName[]" value="{{$fileGroup['fileName'] ?? ''}}">
                        <input type="hidden" class="fileSize" name="fileSize[]" value="{{$fileGroup['fileSize'] ?? ''}}">
                        <input type="hidden" class="fileMD5" name="fileMD5[]" value="{{$fileGroup['fileMD5'] ?? ''}}">
                        <input type="hidden" class="changeTimeDescription" name="changeTimeDescription[]" value="{{$fileGroup['changeTimeDescription'] ?? ''}}">
                        <input type="hidden" class="changeTime" name="changeTime[]" value="{{$fileGroup['changeTime'] ?? ''}}">
                        <input type="text" name="docShortName[]" class="form-control docShortName" placeholder="Обозначение документа" value="{{$fileGroup['docShortName'] ?? ''}}">
                        <input type="text" name="docName[]" class="form-control docName" placeholder="Название документа" value="{{$fileGroup['docName'] ?? ''}}">
                        <input type="text" name="docChanges[]" class="form-control docChanges" placeholder="Версия" value="{{$fileGroup['docChanges'] ?? ''}}">
                        <input type="button" class="btn btn-secondary deleteItems" value="Удалить элементы">
                    </div>
                </div>
                <div class="col-2 d-flex flex-column">
                    <div class="d-flex flex-column align-self-center">
                        <button class="btn btn-secondary file-upload-group-up mb-1" type="button"><i class="fa fa-arrow-circle-up fa-3x" aria-hidden="true"></i></button>
                        <button class="btn btn-secondary file-upload-group-down" type="button"><i class="fa fa-arrow-circle-down fa-3x" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
        <input type="button" class="btn btn-primary" id="addItems" value="Добавить элементы"><br><br><br>

        <div class="d-flex">
            <div class="ms-auto me-auto"><h4>Раздел подписей</h4></div>
        </div>
        @php
            $signGroups = !empty($signUploadGroup) ? $signUploadGroup : [[]];
        @endphp
        @foreach($signGroups as $signGroup)
            <div class="d-flex sign-upload-group">
                <div class="col-10">
                    <div class="mb-3">
                        @if(!empty($signGroup['base64']))
                            <div class="exists-sign">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="exist-sign-file-name">Загружен файл: {{$signGroup['signFileName']}}</div>
                                    <input type="button" class="btn btn-secondary replace-sign" value="Заменить файл">
                                </div>
                            </div>
                            <input style="display: none;" type="file" name="sign[]" class="form-control signInput">
                        @else
                            <input type="file" name="sign[]" class="form-control signInput">
                        @endif
                        <input type="hidden" class="base64" name="base64[]" value="{{$signGroup['base64'] ?? ''}}">
                        <input type="hidden" class="signFileName" name="signFileName[]" value="{{$signGroup['signFileName'] ?? ''}}">
                        <input type="text" name="work[]" class="form-control work" placeholder="Характер работы" value="{{$signGroup['work'] ?? ''}}">
                        <input type="text" name="family[]" class="form-control family" placeholder="Фамилия" value="{{$signGroup['family'] ?? ''}}">
                        <input type="text" name="signDate[]" class="form-control signDate" placeholder="Дата подписания" value="{{$signGroup['signDate'] ?? ''}}">
                        <input type="button" class="btn btn-secondary deleteItemsSign" value="Удалить элементы">
                    </div>
                </div>
                <div class="col-2 d-flex flex-column">
                    <div class="d-flex flex-column align-self-center">
                        <button class="btn btn-secondary sign-upload-group-up mb-1" type="button"><i class="fa fa-arrow-circle-up fa-3x" aria-hidden="true"></i></button>
                        <button class="btn btn-secondary sign-upload-group-down" type="button"><i class="fa fa-arrow-circle-down fa-3x" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
        <input type="button" class="btn btn-primary" id="addItemsSign" value="Добавить элементы"><br><br><br>

        <div class="d-flex">
            <div class="ms-auto me-auto"><h4>Раздел информационного листа</h4></div>
        </div>
        <input class="form-control" type="text" name="infoList" id="infoList" placeholder="Информационно-удостоверяющий лист" value="{{$infoList ?? ''}}">
        <input class="btn btn-primary" type="submit" value="Отправить">
    </form>

    @foreach($fileGroups as $fileGroup)
        @if(!empty($fileGroup['fileName']))
        <br>
        <div>
            <div>Имя: {{$fileGroup['fileName']}}</div>
            <div>MD5: {{$fileGroup['fileMD5'] ?? ''}}</div>
            <div>Размер: {{$fileGroup['fileSize'] ?? ''}}</div>
            <div>{{$fileGroup['changeTimeDescription']}}{{$fileGroup['changeTime']}}</div>
        </div>
        @endif
    @endforeach
    @if(!empty($outputFilename))
        <br><a href="{{Storage::url($outputFilename)}}" target="_blank">Скачать инфо ПДФ</a>
        <div class="mb-5"></div>
    @endif

    @if(!empty($formHistories))
        <br>
        <div>История: </div>
        @foreach($formHistories as $formHistory)
            <div>
                {{$formHistory['infoList']}}: <a href="{{route('formHistory', ['id' => $formHistory['id']])}}">{{$formHistory['created_at']}}</a>
                <a href="{{route('formHistoryDelete', ['id' => $formHistory['id']])}}">Удалить</a>
            </div>
        @endforeach
    @endif
    <div class="mb-5"></div>
</div>
