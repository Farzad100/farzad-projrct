<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Chunk extends Model
{
  protected $guarded = ['id'];

  public static function cleanup_expired_chunks()
  {
    $chunks = Chunk::select('file_id')->where('created_at', '<', Carbon::now()->subHours(3))->get();
    foreach ($chunks as $chunk) {
      $file_id = $chunk->file_id;
      File::deleteDirectory(Doc::chunks_path($file_id));
      Chunk::where('file_id', $file_id)->delete();
    }
    return 1;
  } 
}
