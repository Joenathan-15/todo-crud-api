<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    private function Throw403Error(): JsonResponse
    {
        return response()->json(["code" => "403", "message" => "Action is forbidden"], 403);
    }

    private function Throw404Error(): JsonResponse
    {
        return response()->json(["code" => "404", "message" => "Todo not found"], 404);
    }

    private function Throw500Error($error): JsonResponse
    {
        return response()->json(["code" => "500", "error" => $error], 500);
    }

    public function Create(Request $request): JsonResponse
    {
        $request->validate([
            "content"  => "required"
        ]);
        $current_user = Auth::user();
        if ($current_user) {
            $todo = new Todo();
            try {
                DB::beginTransaction();
                $todo->created_by = $current_user->id;
                $todo->content = $request->content;
                $todo->save();
                DB::commit();
                return response()->json(["code" => "200", "message" => "Todo saved successfuly"], 200);
            } catch (\Exception $err) {
                DB::rollBack();
                return $this->Throw500Error($err);
            }
        } else {
            return $this->Throw403Error();
        }
    }
    public function Show(): JsonResponse
    {
        $current_user = Auth::user();
        if ($current_user) {
            try {
                $todos = Todo::where("created_by", $current_user->id)->get();
                return response()->json(["code" => "200", "todos" => $todos], 200);
            } catch (\Exception $err) {
                return $this->Throw500Error($err);
            }
        } else {
            return $this->Throw403Error();
        }
    }

    public function Change_Status($id): JsonResponse
    {
        $current_user = Auth::user();
        $selected_todo = Todo::find($id);

        if (!$selected_todo) {
            return $this->Throw404Error();
        }
        if (!$current_user || $current_user->id != $selected_todo->created_by) {
            return $this->Throw403Error();
        } else {
            try {
                DB::beginTransaction();
                $selected_todo->status = !$selected_todo->status;
                $selected_todo->save();
                Db::commit();
                return response()->json(["code" => "200", "message" => "Todo status change sucessfully"], 200);
            } catch (\Exception $err) {
                DB::rollBack();
                return $this->Throw500Error($err);
            }
        }
    }

    public function Destroy_todo($id): JsonResponse
    {
        $current_user = Auth::user();
        $selected_todo = Todo::find($id);
        if (!$selected_todo) {
            return $this->Throw404Error();
        }
        if (!$current_user || $current_user->id != $selected_todo->created_by) {
            return $this->Throw403Error();
        } else {
            try {
                DB::beginTransaction();
                $selected_todo->delete();
                DB::commit();
                return response()->json(["code" => "200", "message" => "Todo has been deleted"], 200);
            } catch (\Exception $err) {
                return $this->Throw500Error($err);
            }
        }
    }
}
