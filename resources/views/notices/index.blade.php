@extends('layouts.master')


@section('content')

    
<notices-index v-show="indexMode" :init_model="model" :units="units" :can_edit="can_edit"  :version="version"
             v-on:selected="onSelected" v-on:create="onCreate" >
</notices-index>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    can_edit:false,
                    can_review: false,

                    units: [],

                    creating:false,
                    selected: 0,

                   

                }
            },
            computed: {
                indexMode() {
                    if (this.creating) return false;
                    if (this.selected) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.units = {!! json_encode($units) !!} ;
			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id) {
                  
                    this.selected = id;
                },
                backToIndex() {
                   
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                }

			}

		});



    </script>


@endsection



