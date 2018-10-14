<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserProfile
 *
 * @property int $id
 * @property int $user_id User_id
 * @property string|null $real_name 姓名
 * @property string|null $birthday 生日
 * @property string $avatar 头像路径
 * @property string|null $id_card 身份证
 * @property string|null $phone 手机
 * @property int $sex 性别
 * @property string|null $last_active_at 上次活动时间
 * @property string|null $last_active_ip 上次登录IP
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereLastActiveIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserProfile onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserProfile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserProfile withoutTrashed()
 */
class UserProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'real_name',
        'user_id',
        'birthday',
        'avatar',
        'id_card',
        'phone',
        'sex',
        'last_active_at',
        'last_active_ip',
    ];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
}
