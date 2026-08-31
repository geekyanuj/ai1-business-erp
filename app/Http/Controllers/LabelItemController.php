<?php

namespace App\Http\Controllers;


class LabelItemController extends Controller
{
  


//    public function getLabelDatatable(Request $request)
// {
//     if ($request->ajax()) {

//         $labelItems = LabelItem::with([
//             'label.client'
//         ])->latest();

//         return DataTables::of($labelItems)

//             ->addColumn('lot_no', function ($item) {
//                 return $item->label->lot_no;
//             })

//             ->addColumn('client_name', function ($item) {
//                 return $item->label->client->name ?? '-';
//             })

//             ->addColumn('label_type', function ($item) {
//                 return ucfirst($item->label->label_type);
//             })

//             ->addColumn('actions', function ($item) {
//                 $viewUrl = route('labels.items.show', $item->id);

//                 return '
//                     <a href="' . $viewUrl . '" 
//                        class="btn btn-sm btn-success" 
//                        title="View Details">
//                         <i class="fa fa-eye"></i>
//                     </a>
//                 ';
//             })

//             ->rawColumns(['actions'])
//             ->make(true);
//     }
// }



    
// public function show($id)
// {
//     $label = Label::with([
//         'client',
//         'labelItems.product.clientMappings'
//     ])->findOrFail($id);

//     return view('labels.show', compact('label'));
// }




    


}