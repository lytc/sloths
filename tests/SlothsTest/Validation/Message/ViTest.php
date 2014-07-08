<?php

namespace SlothsTest\Validation\Message;

use Sloths\Translation\Translator;
use Sloths\Validation\Rule\Accepted;
use Sloths\Validation\Rule\After;
use Sloths\Validation\Rule\Alnum;
use Sloths\Validation\Rule\Alpha;
use Sloths\Validation\Rule\Arr;
use Sloths\Validation\Rule\Before;
use Sloths\Validation\Rule\Blank;
use Sloths\Validation\Rule\Bool;
use Sloths\Validation\Rule\Callback;
use Sloths\Validation\Rule\Contains;
use Sloths\Validation\Rule\Date;
use Sloths\Validation\Rule\Digit;
use Sloths\Validation\Rule\Divisible;
use Sloths\Validation\Rule\Domain;
use Sloths\Validation\Rule\Email;
use Sloths\Validation\Rule\EndWith;
use Sloths\Validation\Rule\Equals;
use Sloths\Validation\Rule\Even;
use Sloths\Validation\Rule\Float;
use Sloths\Validation\Rule\GreaterThan;
use Sloths\Validation\Rule\GreaterThanOrEqual;
use Sloths\Validation\Rule\HasAttribute;
use Sloths\Validation\Rule\HasKey;
use Sloths\Validation\Rule\InstOf;
use Sloths\Validation\Rule\Int;
use Sloths\Validation\Rule\Ip;
use Sloths\Validation\Rule\LeapYear;
use Sloths\Validation\Rule\Length;
use Sloths\Validation\Rule\LessThan;
use Sloths\Validation\Rule\LessThanOrEqual;
use Sloths\Validation\Rule\Lower;
use Sloths\Validation\Rule\Match;
use Sloths\Validation\Rule\Max;
use Sloths\Validation\Rule\MaxLength;
use Sloths\Validation\Rule\Min;
use Sloths\Validation\Rule\MinLength;
use Sloths\Validation\Rule\Negative;
use Sloths\Validation\Rule\Not;
use Sloths\Validation\Rule\Null;
use Sloths\Validation\Rule\Numeric;
use Sloths\Validation\Rule\Object;
use Sloths\Validation\Rule\Odd;
use Sloths\Validation\Rule\Positive;
use Sloths\Validation\Rule\Regex;
use Sloths\Validation\Rule\Required;
use Sloths\Validation\Rule\Same;
use Sloths\Validation\Rule\Scalar;
use Sloths\Validation\Rule\StartWith;
use Sloths\Validation\Rule\String;
use Sloths\Validation\Rule\Upper;
use Sloths\Validation\Rule\Url;
use Sloths\Validation\Rule\Xdigit;
use SlothsTest\TestCase;

class ViTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($rule, $message, $negativeMessage)
    {
        $translator = new Translator();
        $translator->setDirectory($rule->getTranslator()->getDirectory());
        $translator->setLocale('vi');
        $rule->setTranslator($translator);

        $this->assertSame($message, $rule->getMessage());
        $this->assertSame($negativeMessage, (new Not($rule))->getMessage());
    }

    public function dataProvider()
    {
        return [
            [new Accepted(), 'Phải chấp nhận', 'Phải không chấp nhận'],
            [new After('2014-06-05'), 'Phải là ngày sau 2014-06-05', 'Phải không là ngày sau 2014-06-05'],
            [new Alnum(), 'Phải chỉ chứa kí tự và số', 'Phải không chỉ chứa kí tự và số'],
            [new Alpha(), 'Phải chỉ chứa kí tự', 'Phải không chỉ chứa kí tự'],
            [new Arr(), 'Phải là kiểu mảng', 'Phải không là kiểu mảng'],
            [new Before('2014-06-05'), 'Phải là ngày trước 2014-06-05', 'Phải không là ngày trước 2014-06-05'],
            [new Blank(), 'Phải là giá trị NULL, chuỗi hoặc mảng rỗng', 'Phải không là giá trị NULL, chuỗi hoặc mảng rỗng'],
            [new Bool(), 'Phải là giá trị TRUE hoặc FALSE', 'Phải không là giá trị TRUE hoặc FALSE'],
            [new Callback(), 'Phải là giá trị callback hợp lệ', 'Phải không là giá trị callback'],
            [new Contains('foo'), 'Phải chứa foo', 'Phải không chứa foo'],
            [new Date(), 'Phải là ngày hợp lệ', 'Phải không là ngày'],
            [new Digit(), 'Phải chỉ chứa kí số', 'Phải không chỉ chứa kí số'],
            [new Divisible(2), 'Phải chia hết cho 2', 'Phải không chia hết cho 2'],
            [new Domain(), 'Phải là tên miền hợp lệ', 'Phải không là tên miền hợp lệ'],
            [new Email(), 'Phải là địa chỉ email hợp lệ', 'Phải không là địa chỉ email hợp lệ'],
            [new EndWith('foo'), 'Phải kết thúc với foo', 'Phải không kết thúc với foo'],
            [new Equals('foo'), 'Phải bằng với foo', 'Phải không bằng với foo'],
            [new Equals(true), 'Phải bằng với TRUE', 'Phải không bằng với TRUE'],
            [new Equals(1), 'Phải bằng với 1', 'Phải không bằng với 1'],
            [new Equals([]), 'Phải bằng với giá trị có kiểu array', 'Phải không bằng với giá trị có kiểu array'],
            [new Even(), 'Phải là số chẵn', 'Phải không là số chẵn'],
            [new Float(), 'Phải là số thực', 'Phải không là số thực'],
            [new GreaterThan(1), 'Phải lớn hơn 1',  'Phải không lớn hơn 1'],
            [new GreaterThan([]), 'Phải lớn hơn giá trị có kiểu array', 'Phải không lớn hơn giá trị có kiểu array'],
            [new GreaterThanOrEqual(1), 'Phải lớn hơn hoặc bằng 1', 'Phải không lớn hơn hoặc bằng 1'],
            [new GreaterThanOrEqual([]), 'Phải lớn hơn hoặc bằng giá trị có kiểu array',  'Phải không lớn hơn hoặc bằng giá trị có kiểu array'],
            [new HasAttribute('foo'), 'Phải có thuộc tính foo', 'Phải không có thuộc tính foo'],
            [new HasKey('foo'), 'Phải có khoá foo', 'Phải không có khoá foo'],
            [new InstOf('Exception'), 'Phải là khởi tạo của lớp Exception', 'Phải không là khởi tạo của lớp Exception'],
            [new Int(), 'Phải là số nguyên' ,'Phải không là số nguyên'],
            [new Ip(), 'Phải là địa chỉ IP', 'Phải không là địa chỉ IP'],
            [new LeapYear(), 'Phải là năm nhuận', 'Phải không là năm nhuận'],
            [new Length(2), 'Phải có chiều dài là 2', 'Phải không có chiều dài là 2'],
            [new LessThan(1), 'Phải lớn hơn 1', 'Phải không lớn hơn 1'],
            [new LessThan([]), 'Phải lớn hơn giá trị có kiểu array', 'Phải không lớn hơn giá trị có kiểu array'],
            [new LessThanOrEqual(1), 'Phải lớn hơn hoặc bằng 1', 'Phải không lớn hơn hoặc bằng 1'],
            [new LessThanOrEqual([]), 'Phải lớn hơn hoặc bằng giá trị có kiểu array', 'Phải không lớn hơn hoặc bằng giá trị có kiểu array'],
            [new Lower(), 'Phải là chữ thường', 'Phải không là chữ thường'],
            [new Match('/foo/'), 'Phải hợp với mẫu /foo/', 'Phải không hợp với mẫu /foo/'],
            [new Max(1), 'Phải nhỏ hơn 1', 'Phải không nhỏ hơn 1'],
            [new Max(1, true), 'Phải nhỏ hơn hoặc bằng 1', 'Phải không nhỏ hơn hoặc bằng 1'],
            [new MaxLength(1), 'Phải có chiều dài nhỏ hơn 1', 'Phải không có chiều dài nhỏ hơn 1'],
            [new Min(1), 'Phải lớn hơn 1', 'Phải không lớn hơn 1'],
            [new Min(1, true), 'Phải lớn hơn hoặc bằng 1', 'Phải không lớn hơn hoặc bằng 1'],
            [new MinLength(1), 'Phải có chiều dài lớn hơn 1', 'Phải không có chiều dài lớn hơn 1'],
            [new Negative(), 'Phải lớn hơn 0', 'Phải không lớn hơn 0'],
            [new Null(), 'Phải là NULL', 'Phải không là NULL'],
            [new Numeric(), 'Phải là kiểu số', 'Phải không là kiểu số'],
            [new Object(), 'Phải là kiểu đối tượng', 'Phải không là kiểu đối tượng'],
            [new Odd(), 'Phải là số lẽ', 'Phải không là số lẽ'],
            [new Positive(), 'Phải nhỏ hơn 0', 'Phải không nhỏ hơn 0'],
            [new Regex(), 'Phải là biểu thức chính quy', 'Phải không là biểu thức chính quy'],
            [new Required(), 'Là bắt buộc', 'Là không bắt buộc'],
            [new Same(1), 'Phải là 1', 'Phải không là 1'],
            [new Same('foo'), 'Phải là foo', 'Phải không là foo'],
            [new Same([]), 'Phải là giá trị có kiểu array', 'Phải không là giá trị có kiểu array'],
            [new Scalar(), 'Phải là số, chuỗi hoặc kiểu luận lí', 'Phải không là số, chuỗi và kiểu luận lí'],
            [new StartWith('foo'), 'Phải bắt đầu với foo', 'Phải không bắt đầu với foo'],
            [new String(), 'Phải là kiểu chuỗi', 'Phải không là kiểu chuỗi'],
            [new Upper(), 'Phải là chữ hoa', 'Phải không là chữ hoa'],
            [new Url(), 'Phải là URL', 'Phải không là URL'],
            [new Xdigit(), 'Phải chỉ chứa kí tự hệ thập lục phân', 'Phải không chỉ chứa kí tự hệ thập lục phân']
        ];
    }
}