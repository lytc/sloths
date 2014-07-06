<?php

namespace SlothsTest\Validation\Message;

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

class DefaultLocaleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($rule, $message, $negativeMessage)
    {
        $this->assertSame($message, $rule->getMessage());
        $this->assertSame($negativeMessage, (new Not($rule))->getMessage());
    }

    public function dataProvider()
    {
        return [
            [new Accepted(), 'Must be accepted', 'Must not be accepted'],
            [new After('2014-06-05'), 'Must be a date after 2014-06-05', 'Must not be a date after 2014-06-05'],
            [new Alnum(), 'Must contain only letters and numbers', 'Must not contain only letters and numbers'],
            [new Alpha(), 'Must contain only letters', 'Must not contain only letters'],
            [new Arr(), 'Must be an array', 'Must not be an array'],
            [new Before('2014-06-05'), 'Must be a date before 2014-06-05', 'Must not be a date before 2014-06-05'],
            [new Blank(), 'Must be NULL, an empty string or an empty array', 'Must not be NULL, an empty string or an empty array'],
            [new Bool(), 'Must be TRUE or FALSE', 'Must not be TRUE or FALSE'],
            [new Callback(), 'Must be a valid callback', 'Must not be a valid callback'],
            [new Contains('foo'), 'Must contain foo', 'Must not contain foo'],
            [new Date(), 'Must be a valid date', 'Must not be a date'],
            [new Digit(), 'Must contain only digits', 'Must not contain only digits'],
            [new Divisible(2), 'Must be divisible by 2', 'Must not be divisible by 2'],
            [new Domain(), 'Must be a valid domain name', 'Must not be a domain name'],
            [new Email(), 'Must be a valid email address', 'Must not be an email address'],
            [new EndWith('foo'), 'Must end with foo', 'Must not end with foo'],
            [new Equals('foo'), 'Must be equals to foo', 'Must not be equals to foo'],
            [new Equals(true), 'Must be equals to TRUE', 'Must not be equals to TRUE'],
            [new Equals(1), 'Must be equals to 1', 'Must not be equals to 1'],
            [new Equals([]), 'Must be equals to given array', 'Must not be equals to given array'],
            [new Even(), 'Must be an even number', 'Must not be an even number'],
            [new Float(), 'Must be a float number', 'Must not be a float number'],
            [new GreaterThan(1), 'Must be greater than 1',  'Must not be greater than 1'],
            [new GreaterThan([]), 'Must be greater than given array', 'Must not be greater than given array'],
            [new GreaterThanOrEqual(1), 'Must be greater than or equal to 1', 'Must not be greater than or equal to 1'],
            [new GreaterThanOrEqual([]), 'Must be greater than or equal to given array',  'Must not be greater than or equal to given array'],
            [new HasAttribute('foo'), 'Must have an attribute foo', 'Must not have an attribute foo'],
            [new HasKey('foo'), 'Must have the key foo', 'Must not have the key foo'],
            [new InstOf('Exception'), 'Must be an instanceof class Exception', 'Must not be an instanceof class Exception'],
            [new Int(), 'Must be a integer number' ,'Must not be a integer number'],
            [new Ip(), 'Must be an IP address', 'Must not be an IP address'],
            [new LeapYear(), 'Must be a leap year', 'Must not be a leap year'],
            [new Length(2), 'Must have length is 2', 'Must not have length is 2'],
            [new LessThan(1), 'Must be less than 1', 'Must not be less than 1'],
            [new LessThan([]), 'Must be less than given array', 'Must not be less than given array'],
            [new LessThanOrEqual(1), 'Must be less than or equal to 1', 'Must not be less than or equal to 1'],
            [new LessThanOrEqual([]), 'Must be less than or equal to given array', 'Must not be less than or equal to given array'],
            [new Lower(), 'Must be lowercase', 'Must not be lowercase'],
            [new Match('/foo/'), 'Must match pattern /foo/', 'Must not match pattern /foo/'],
            [new Max(1), 'Must be less than 1', 'Must not be less than 1'],
            [new Max(1, true), 'Must be less than or equal to 1', 'Must not be less than or equal to 1'],
            [new MaxLength(1), 'Must have length less than or equal to 1', 'Must not have length less than or equal to 1'],
            [new Min(1), 'Must be greater than 1', 'Must not be greater than 1'],
            [new Min(1, true), 'Must be greater than or equal to 1', 'Must not be greater than or equal to 1'],
            [new MinLength(1), 'Must have length greater than or equal to 1', 'Must not have length greater than or equal to 1'],
            [new Negative(), 'Must be greater than 0', 'Must not be greater than 0'],
            [new Null(), 'Must be NULL', 'Must not be NULL'],
            [new Numeric(), 'Must be numeric characters', 'Must not be numeric characters'],
            [new Object(), 'Must be an object', 'Must not be an object'],
            [new Odd(), 'Must be an odd number', 'Must not be an odd number'],
            [new Positive(), 'Must be less than 0', 'Must not be less than 0'],
            [new Regex(), 'Must be a regular expression pattern', 'Must not be a regular expression pattern'],
            [new Required(), 'Is required', 'Is not required'],
            [new Same(1), 'Must be the same of 1', 'Must not be the same of 1'],
            [new Same('foo'), 'Must be the same of foo', 'Must not be the same of foo'],
            [new Same([]), 'Must be the same of given array', 'Must not be the same of given array'],
            [new Scalar(), 'Must be a number, string or boolean type', 'Must not be a number, string and boolean type'],
            [new StartWith('foo'), 'Must be start with foo', 'Must not be start with foo'],
            [new String(), 'Must be a string', 'Must not be a string'],
            [new Upper(), 'Must be uppercase', 'Must not be uppercase'],
            [new Url(), 'Must be a valid URL', 'Must not be an URL'],
            [new Xdigit(), 'Must contain only hexadecimal digits', 'Must not contain only hexadecimal digits']
        ];
    }
}