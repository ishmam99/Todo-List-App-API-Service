<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'  ];

        /**
         * Get the user that owns the todo.
         */
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    
}
