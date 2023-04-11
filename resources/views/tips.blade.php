Help
<div class="row">
<div class="elem group_elem col-sm-12">Use texts
        <pre>
text1
text2
...
</pre>
        Or key-texts
        <pre>
key1 : text1
key2 : text2
...
</pre>
    </div>
    <div class="elem select_elem col-sm-12">
        Or load data from url:
        <pre>
//methods:
#options_url : /admin/api/mydata
</pre>
    </div>
    <div class="elem textarea_elem col-sm-12">
        <pre>
//methods:
#rows : 5
</pre>
    </div>
    <div class="elem number_elem col-sm-12">
        <pre>
//methods:
#max : 100
#min : 1
</pre>
    </div>
    <div class="elem editor_elem col-sm-12">
        <pre>#element : ueditor </pre>
    </div>
    <div class="elem color_elem col-sm-12">
        <pre>
//methods:
#format : rgba
//color format can be : [hex, rgb, rgba]

</pre>
    </div>
    <div class="col-sm-12">
        Common Methods:
        <pre>
#required
#rules : required|min:3|max:12
#width : 6, 2
//etc..
//#methodname : arg1, arg2 ...
//suported args types [string/integer/folat/boolean]
</pre>
    </div>
    <div class="elem normal_elem col-sm-12">
        Replace default element:
        <pre>
#element : mobile
//#element : ip
//#element : url
//#element : email
//#element : currency
//etc..
</pre>
        Extend element:
        <pre>
//Extend element in Admin/bootstrap.php :
Form::extend('new_element', App/Admin/Extensions/NewElement::class);
//Useage
#element : new_element
</pre>
    </div>
    <div class="elem image_elem col-sm-12">
        //Image Methods:
        <pre>
#uniqueName
#sequenceName
#removable
#move : newdir, newname
#dir : newdir
#name : newname
#resize : 320, 240
#insert : storage/public/watermark.png ,center
#crop : 320, 240, 0, 0
//etc..
</pre>
        //Some methods require intervention/image <a href="http://image.intervention.io/getting_started/installation" target="_blank">[installation]</a>
        <br />
        //Usage : <a href="http://image.intervention.io/getting_started/introduction" target="_blank">[Intervention]</a>
    </div>
    <div class="elem file_elem col-sm-12">
        //File Methods:
        <pre>
#uniqueName
#sequenceName
#removable
#downloadable
#retainable
//multiple
#sortable
#move : newdir, newname
#dir : newdir
#name : newname
//etc..
</pre>
    </div>
    <div class="elem map_elem col-sm-12">
        <pre>
//To use map ,you need to edit configs first.
//map_provider in /config/admin.php
//TENCENT_MAP_API_KEY or GOOGLE_API_KEY in /.env
</pre>
    </div>
    <div class="elem table_elem col-sm-12">
        <pre>
key1 : label1
key2 : label2
...
</pre>
    </div>
</div>