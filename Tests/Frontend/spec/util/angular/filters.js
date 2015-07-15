
describe('Test custom angular filters', function () {
    var filter;

    beforeEach(function(){
        console.log(module);
        module('homie');

        inject(function($injector){
            filter = $injector.get('$filter');
        });
    });

    it('should capitalize a string', function () {
        // Arrange.
        var foo = 'hello world', result;

        // Act.
        result = filter('testFilter')(foo, 'capitalize');

        // Assert.
        expect(result).toEqual('HELLO WORLD');
    });
});
