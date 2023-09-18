<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Blog;
use App\Models\BlogLike;
use DB;

class BlogController extends Controller
{
   
    public function blog(Request $request) {
        try {
            $userInfo = $request->user();
            $userId = $userInfo->id;

            if (!empty($request->id)) {
                $blog = Blog::find($request->id);
            } else {
                $blog = new Blog;
            }
          
            $blog->user_id = $userId;
            $blog->title = isset($request->title) ? $request->title:$blog->title;;
            $slugs = Str::slug($blog->title, "-");
            $blog->slug = $slugs;
            $blog->description = isset($request->description) ? $request->description:$blog->description;;
            $blog->page_description = isset($request->page_description) ? $request->page_description:$blog->page_description;            ;
           
            if ($request->hasFile('blog_image')) {
                $web = $request->file('blog_image');
                $webname = time() . '.' . $web->getClientOriginalExtension();
                $web->move(public_path('/images/blog/'), $webname);
                $blog->blog_image = $webname;
            } else {
                $blog->blog_image = isset($request->blog_image) ? $request->blog_image:$blog->blog_image;
            }

            if (!empty($request->id)) {
                $blog->updated_at = date('Y-m-d H:i:s');
            } else {
                $blog->created_at = date('Y-m-d H:i:s');
            }
            $blog->is_active = isset($request->is_active) ? $request->is_active : "1";
            $blog->is_delete = isset($request->is_delete) ? $request->is_delete : "0";
            $data = $blog->save();
            $status = 201;
            if ($data) {
                $status = 200;
                $response['status'] = "1";
                $response['title'] = "successfully";
                $response['message'] = "Blog has been " . (!empty($request->id) ? "updated" : "created") . " sucessfully.";
            } else {
                $status = 201;
                $response['status'] = "0";
                $response['title'] = "failure";
                $response['message'] = "Oops! something went wrong.";
            }
            return response($response, $status);
        } catch (\Exception $e) {
            $response['status'] = "0";
            $response['Error'] = $e->getMessage();
            return response($response, 500);
        }
    }

  
    public function get_single_details($id) {
        $blog = Blog::find($id);
        if (!$blog) {
            $response['status'] = "0";
            $response['title'] = "No record";
            $response['message'] = "No record was found.";
            return response($blog, 200);
        }
 
        $response['status'] = "1";
        $response['data'] = $blog;
        return response($response, 200);
    }

    public function blog_list_api(Request $request) {
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $skip = $request->get('page') ? $request->get('page') : 0;
        $skip = $skip * $limit;
        $sortField = $request->get('sort') ? $request->get('sort') : 'id';
        $sortType = $request->get('order') ? $request->get('order') : 'asc';
        $search = $request->get('search') ? $request->get('search') : '';
        $like = $request->get('like') ? $request->get('like'):'';
      
        $userInfo = $request->user('sanctum');
        if(!empty($userInfo)){
            $userId = $userInfo->id;

            $items =  Blog::where('blog.is_delete','0')->where('blog.is_active', '1')->where('user_id',$userId)
                ->select('blog.*',DB::raw('count(bloglike.blog_id) AS count'))
                ->leftJoin('bloglike','blog.id','=','bloglike.blog_id')
                ->groupBy('blog.id');
        }else{
            $items =  Blog::where('blog.is_delete','0')->where('blog.is_active', '1')
                ->select('blog.*',DB::raw('count(bloglike.blog_id) AS count'))
                ->leftJoin('bloglike','blog.id','=','bloglike.blog_id')
                ->groupBy('blog.id');
        }

        if(!empty($like)){
            $items = $items->orderBy('count','DESC');
        }else{
          $items = $items->orderBy($sortField, $sortType);
        }
        if(!empty($search)){
            $items = $items->search($search);  
        }
        $items = $items->limit($limit)
                ->skip($skip)
                ->get();
        
        $allListing = array();
        if (count($items) > 0) {
            foreach ($items as $blog) {
                if($blog->count > 0){
                    $blog['is_liked'] = 'true';
                    $blog['most_like'] = $blog->count;
                   
                }
                unset($blog['count']);
                $data = json_decode(json_encode($blog), true);
                $allListing[] = $data;
            }
        }

            
     
        $dataArray = array(
            "data" => $allListing
        );
        $status = 200;
        $response = $dataArray;
        $response['status'] = count($allListing) > 0 ? "1" : "0";
        $response['title'] = "successfully";
        $response['message'] = "Records has been fetched successfully.";
        return response($response, $status);
    }

    public function blog_like(Request $request){

        $blog_id = $request->blog_id;

        $blog = Blog::find($blog_id);
        if (!$blog) {
            $response['status'] = "0";
            $response['title'] = "No record";
            $response['message'] = "No record was found.";
            return response($blog, 200);
        }else{
            $userInfo = $request->user('sanctum');
            if(!empty($userInfo)){
                $userId = $userInfo->id;
                $guest = NULL;
            }else{
                $userId = NULL;
                $guest = 1;
            }

            if(!empty($userId)){
                $blog_like = BlogLike::where('blog_id',$blog_id)->where('ip',$request->ip())->where('user_id',$userId)->first(); 
            }else{
                $blog_like = BlogLike::where('blog_id',$blog_id)->where('ip',$request->ip())->where('guest','1')->first(); 
            }
          
            if (!empty($blog_like)) {

                $blog_like->where('id',$blog_like->id)->delete();

                $status = 200;
                $response['status'] = "1";
                $response['title'] = "successfully";
                $response['message'] = "Blog unlike successfully.";
                return response($response, $status);
            } else {
                $blog_like = new BlogLike;
                $blog_like->blog_id = $blog_id;
                $blog_like->user_id = $userId;
                $blog_like->guest = $guest;
                $blog_like->ip = $request->ip();
                $blog_like->like = 1;
                $blog_like->save();

                $status = 200;
                $response['status'] = "1";
                $response['title'] = "successfully";
                $response['message'] = "Blog like successfully.";
                return response($response, $status);

            }
        }
        
    }
}
