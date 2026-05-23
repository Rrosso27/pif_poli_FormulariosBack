<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Questions
 *
 * @property int $id
 * @property int $user_id
 * @property string $question_text
 * @property string $question_type
 * @property array|null $options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Questions extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the survey that owns the question.
     */
    public function survey()
    {
        return $this->belongsTo(Surveys::class, 'survey_id');
    }

    /**
     * Get the responses for the question.
     */
    public function responses()
    {
        return $this->hasMany(Questions::class, 'question_id');
    }
}
