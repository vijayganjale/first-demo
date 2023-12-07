<?php

namespace App\Http\Controllers;
use App\Models\AvailableStock;
use App\Models\UsedStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoffeeMachineController extends Controller
{
    public function form_page(){
    
      $UsedStock = UsedStock::all();
      $UsedStock_here = UsedStock::all()->count();
        return view('welcome',['userss'=>$UsedStock,'use'=>$UsedStock_here]);
      }



      public function cups_quantity(Request $request){

        $available = AvailableStock::all()->first();

         $ten_milk  = $available['total_milk'] * 10/100;
         $ten_sugar  = $available['total_sugar'] * 10/100;
         $ten_coffee  = $available['total_coffee'] * 10/100;

          $cups_milk = ( $available['milk'] - $ten_milk ) / 100  ;
          $cups_sugar = ( $available['sugar'] - $ten_sugar ) / 50  ;
          $cups_coffee = ( $available['coffee'] - $ten_coffee ) / 10  ;
      
          $ingredients = [
            'milk' => $cups_milk,
            'sugar' => $cups_sugar,
            'coffee' => $cups_coffee
        ];
    
        
        // Find the ingredient with the smallest quantity
        $smallest_ingredient = array_search(min($ingredients), $ingredients);
        $smallest_quantity = min($cups_milk, $cups_sugar, $cups_coffee);
        
     
         $remaining_milk =  $available['milk'] - (100 * $request->quantity_of_cup) ;
         $remaining_sugar =  $available['sugar'] - (50 * $request->quantity_of_cup) ;
         $remaining_coffee =  $available['coffee'] - (10 * $request->quantity_of_cup) ;

      if($remaining_milk >= $ten_milk && $remaining_sugar >= $ten_sugar && $remaining_coffee >= $ten_coffee)

      {      

        AvailableStock::where('id', 1)->update([
            'milk' =>$remaining_milk,
            'coffee' =>$remaining_coffee,
            'sugar' =>$remaining_sugar,
            
            ]);
      
        UsedStock::insert([
            'milk' =>100 * $request->quantity_of_cup,
            'coffee' =>10 *  $request->quantity_of_cup,
            'sugar' =>50 * $request->quantity_of_cup,
            'cups_quantity' => $request->quantity_of_cup,
            ]);
        return redirect()->back()->with('message',$request->quantity_of_cup. '  '.'cup coffee prepared successfully , have a nice day.... ');
        }

        else {
          return redirect()->back()->with('message', 'Please check the stock ' . $smallest_ingredient . ' and refill the stock. You can only make ' . $smallest_quantity . ' cups.');
      }
      

     
      }

}
