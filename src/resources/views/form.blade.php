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
        @foreach($fileGroups as $fileGroup)
            <div class="file-upload-group mb-3">
                <input type="hidden" class="fileModifiedTime" name="fileModifiedTime[]" value="{{$fileGroup['fileModifiedTime'] ?? ''}}">
                <input type="file" name="file[]" class="form-control fileInput" value="">
                <input type="text" name="docShortName[]" class="form-control docShortName" placeholder="Обозначение документа" value="{{$fileGroup['docShortName'] ?? ''}}">
                <input type="text" name="docName[]" class="form-control docName" placeholder="Название документа" value="{{$fileGroup['docName'] ?? ''}}">
                <input type="text" name="docChanges[]" class="form-control docChanges" placeholder="Версия" value="{{$fileGroup['docChanges'] ?? ''}}">
                <input type="button" class="btn btn-secondary deleteItems" value="Удалить элементы">
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
            <div class="sign-upload-group mb-3">
                <input type="file" name="sign[]" class="form-control signInput">
                <input type="text" name="work[]" class="form-control work" placeholder="Характер работы" value="{{$signGroup['work'] ?? ''}}">
                <input type="text" name="family[]" class="form-control family" placeholder="Фамилия" value="{{$signGroup['family'] ?? ''}}">
                <input type="text" name="signDate[]" class="form-control signDate" placeholder="Дата подписания" value="{{$signGroup['signDate'] ?? ''}}">
                <input type="button" class="btn btn-secondary deleteItemsSign" value="Удалить элементы">
            </div>
        @endforeach
        <input type="button" class="btn btn-primary" id="addItemsSign" value="Добавить элементы"><br><br><br>

        <div class="d-flex">
            <div class="ms-auto me-auto"><h4>Раздел информационного листа</h4></div>
        </div>
        <input class="form-control" type="text" name="infoList" id="infoList" placeholder="Информационно-удостоверяющий лист" value="{{$infoList ?? ''}}">
        <label class="form-label" for="list">Лист</label>
        <input class="form-control" type="text" name="list" id="list" value="{{$list ?? ''}}">
        <label class="form-label" for="allLists">Всего листов</label>
        <input class="form-control" type="text" name="allLists" id="allLists" value="{{$allLists ?? ''}}">
        <input class="btn btn-primary" type="submit" value="Отправить">
    </form>

    @if(!empty($fileInfo))
        @foreach($fileInfo as $fileIn)
            <br>
            <div>
                <div>Имя: {{$fileIn['fileName'] ?? ''}}</div>
                <div>MD5: {{$fileIn['fileMD5'] ?? ''}}</div>
                <div>Размер: {{$fileIn['fileSize'] ?? ''}}</div>
                <div>{{$fileIn['changeTimeDescription']}}{{$fileIn['changeTime']}}</div>
            </div>
        @endforeach
        <br><a href="{{Storage::url($outputFilename)}}" target="_blank">Скачать инфо ПДФ</a>
    @endif

    @if(!empty($formHistories))
        <br>
        <div>История: </div>
        @foreach($formHistories as $formHistory)
            <a href="{{route('formHistory', ['id' => $formHistory['id']])}}">{{$formHistory['created_at']}}</a>
            <a href="{{route('formHistoryDelete', ['id' => $formHistory['id']])}}">Удалить</a>
            <br>
        @endforeach
    @endif
    <div class="mb-5"></div>
</div>
